<?php
// Validate input to avoid attacks
preg_match('/^[a-zA-Z0-9_]+$/',$_REQUEST["card_location"]) ? $card_location = $_REQUEST["card_location"] : exit('XSS is detected!');

// Start the session and init information
session_start();
$game_id = $_SESSION['game_id'];
$nickname = $_SESSION['nickname'];

// Connect to the database of users and games
$db_ini = parse_ini_file('Arcomage.ini');
$mysqli = new mysqli($db_ini['host'], $db_ini['username'], $db_ini['password'], $db_ini['db']);
if ($mysqli->connect_errno) echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

// Get the player game stat and played card
$my_game_stat = get_my_game_stat();
$opponent_game_stat = get_enemy_game_stat();

function main()
{
    global $mysqli, $game_id, $my_game_stat, $opponent_game_stat, $played_card;
    
    // Check if player's turn
    if ($my_game_stat['current_flag'] == false) {
        die("Error: Not Your Turn!");
    }

    $my_cards = array($my_game_stat['card1_id'],  $my_game_stat['card2_id'], $my_game_stat['card3_id'], $my_game_stat['card4_id'], $my_game_stat['card5_id'], $my_game_stat['card6_id']);
    if (in_array($played_card, $my_cards))
    {
        // Play the card
        $play_card_result = play_card($played_card);

        $stmt = $mysqli->prepare("UPDATE games SET last_played_card = ? WHERE game_id = ?;");
        $stmt->bind_param('si', $played_card, $game_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['cards_played'] += 1; 

        // Check if anyone wins after played card
        check_for_win();

        get_new_card();

        switch ($play_card_result) 
        {
            case 0: // Card Discarded
                echo "Your Card Will be Discarded!";
                break;

            case 1: // Played card one turn only
                break;

            case 2: // Played card got another turn
                update_resources('gems', 0, $my_game_stat['magic']);
                update_resources('bricks', 0, $my_game_stat['quarry']);
                update_resources('recruits', 0, $my_game_stat['dungeon']);
                exit("You have another Turn");
                break;
        }

        // Update enemy resources for begining of his turn
        update_resources('gems', 1, $opponent_game_stat['magic']);
        update_resources('bricks', 1, $opponent_game_stat['quarry']);
        update_resources('recruits', 1, $opponent_game_stat['dungeon']);

        // Check if anyone wins after update resources
        check_for_win();

        // Switch Turns
        update_resources('current_flag', 0, -1);
        update_resources('current_flag', 1, 1);
    }
    else
    {
        echo "Player Don't Hold Card " . $played_card;
    }
}

function end_game($end_result)
{
    global $mysqli, $game_id, $nickname;

    // Get the current statistics for both player
    $statistics = get_users_statistics();
    
    // Put the results in correct user array
    ($statistics[0]['nickname'] == $nickname) ? $user_statistics = $statistics[0] : $user_statistics = $statistics[1];
    ($statistics[0]['nickname'] != $nickname) ? $opponent_statistics = $statistics[0] : $opponent_statistics = $statistics[1];
    
    // Set the nickname of opponent and games played for both users
    $opponent_nickname = $opponent_statistics['nickname'];
    $user_games_played = $user_statistics['games_played'] + 1;
    $opponent_games_played = $opponent_statistics['games_played'] + 1;

    if ( ($end_result >= 1) && ($end_result <= 3) )
    {
        $user_result = "1";
        $opponent_result = "2";
        
        // User's new statistics
        $user_games_won = $user_statistics['games_won'] + 1;
        $user_num_surrender_wins = $user_statistics['num_surrender_wins']; 
        $user_num_resources_wins = $user_statistics['num_resources_wins'];
        $user_num_tower_wins = $user_statistics['num_tower_wins'];
        $user_num_destroy_wins = $user_statistics['num_destroy_wins'];

        // Opponents new statistics
        $opponent_games_lost = $opponent_statistics['games_lost'] + 1;
        $opponent_num_surrender_loses = $opponent_statistics['num_surrender_loses']; 
        $opponent_num_resources_loses = $opponent_statistics['num_resources_loses'];
        $opponent_num_tower_loses = $opponent_statistics['num_tower_loses'];
        $opponent_num_destroy_loses = $opponent_statistics['num_destroy_loses'];
        
        switch ($end_result)
        {
            case 1: // resources win
                $user_num_resources_wins += 1;
                $opponent_num_resources_loses += 1;
                break;

            case 2: // tower win
                $user_num_tower_wins += 1;
                $opponent_num_tower_loses += 1;
                break;

            case 3: // destory win
                $user_num_destroy_wins += 1;
                $opponent_num_destroy_loses += 1;
                break;
        }

        // Max / Min cards update
        ($_SESSION['cards_played'] > $user_statistics['win_max_cards']) ? $max_cards_played = $_SESSION['cards_played'] : $max_cards_played = $user_statistics['win_max_cards'];
        ($_SESSION['cards_played'] < $user_statistics['win_min_cards']) ? $min_cards_played = $_SESSION['cards_played'] : $min_cards_played = $user_statistics['win_min_cards'];

        // Setup User's query
        $stmt = $mysqli->prepare("UPDATE users SET games_won = ?,   
                                        games_played = ?, 
                                        num_surrender_wins = ?, 
                                        num_resources_wins = ?, 
                                        num_tower_wins = ?, 
                                        num_destroy_wins = ?, 
                                        win_max_cards = ?, 
                                        win_min_cards = ?, 
                                        last_game_result = ? 
                                        WHERE nickname = ?;");
        $stmt->bind_param('iiiiiiiiis', $user_games_won, $user_games_played, $user_num_surrender_wins, $user_num_resources_wins, $user_num_tower_wins, 
                                        $user_num_destroy_wins, $max_cards_played, $min_cards_played, $user_result, $nickname);
        $stmt->execute();
        $stmt->close();

        // Setup Opponent's query
        $stmt = $mysqli->prepare("UPDATE users SET games_lost = ?,   
                                                    games_played = ?, 
                                                    num_surrender_loses = ?, 
                                                    num_resources_loses = ?, 
                                                    num_tower_loses = ?, 
                                                    num_destroy_loses = ?, 
                                                    last_game_result = ? 
                                                    WHERE nickname = ?");
        $stmt->bind_param('iiiiiiis', $opponent_games_lost, $opponent_games_played, $opponent_num_surrender_loses, $opponent_num_resources_loses, $opponent_num_tower_loses, 
                                        $opponent_num_destroy_loses, $opponent_result, $opponent_nickname);
        $stmt->execute();
        $stmt->close();
    }
    
    else if ( ($end_result == 0) || (($end_result >= 4) && ($end_result <= 6)) )
    {
        $user_result = "2";
        $opponent_result = "1";
        
        // User's new statistics
        $user_games_lost = $user_statistics['games_lost'] + 1;
        $user_num_surrender_loses = $user_statistics['num_surrender_loses']; 
        $user_num_resources_loses = $user_statistics['num_resources_loses'];
        $user_num_tower_loses = $user_statistics['num_tower_loses'];
        $user_num_destroy_loses = $user_statistics['num_destroy_loses'];

        // Opponents new statistics
        $opponent_games_won = $opponent_statistics['games_won'] + 1;
        $opponent_num_surrender_wins = $opponent_statistics['num_surrender_wins']; 
        $opponent_num_resources_wins = $opponent_statistics['num_resources_wins'];
        $opponent_num_tower_wins = $opponent_statistics['num_tower_wins'];
        $opponent_num_destroy_wins = $opponent_statistics['num_destroy_wins'];
        
        switch ($end_result)
        {
            case 0: // surrender lose
                $user_num_surrender_loses += 1;
                $opponent_num_surrender_wins += 1;
                break;

            case 4: // resources lose
                $user_num_resources_loses += 1;
                $opponent_num_resources_wins += 1;
                break;

            case 5: // tower lose
                $user_num_tower_loses += 1;
                $opponent_num_tower_wins += 1;
                break;

            case 6: // destory lose
                $user_num_destroy_loses += 1;
                $opponent_num_destroy_wins += 1;
                break;
        }

        // Setup User's query
        $stmt = $mysqli->prepare("UPDATE users SET games_lost = ?, 
                                                games_played = ?,  
                                                num_surrender_loses = ?, 
                                                num_resources_loses = ?, 
                                                num_tower_loses = ?, 
                                                num_destroy_loses = ?, 
                                                last_game_result = ?  
                                                WHERE nickname = ?");
        $stmt->bind_param('iiiiiiis', $user_games_lost, $user_games_played, $user_num_surrender_loses, $user_num_resources_loses, $user_num_tower_loses, 
                                    $user_num_destroy_loses, $user_result, $nickname);
        $stmt->execute();
        $stmt->close();

        // Setup Opponent's query
         $stmt = $mysqli->prepare("UPDATE users SET games_won = ?,   
                                                    games_played = ?, 
                                                    num_surrender_wins = ?, 
                                                    num_resources_wins = ?, 
                                                    num_tower_wins = ?, 
                                                    num_destroy_wins = ?, 
                                                    last_game_result = ? 
                                                    WHERE nickname = ?");
        $stmt->bind_param('iiiiiiis', $opponent_games_won, $opponent_games_played, $opponent_num_surrender_wins, $opponent_num_resources_wins, $opponent_num_tower_wins, 
                            $opponent_num_destroy_wins, $opponent_result, $opponent_nickname);
        $stmt->execute();
        $stmt->close();
    }

    else return; // No such option
    
    $stmt = $mysqli->prepare("UPDATE games SET game_end_status = ? WHERE game_id = ? AND nickname = ?;");
    $stmt->bind_param('iis', $user_result, $game_id, $nickname);
    $stmt->execute();
    $stmt->bind_param('iis', $opponent_result, $game_id, $opponent_nickname);
    $stmt->execute();
    $stmt->close();

    exit("GameOver");
}

function get_my_game_stat()
{
    global $mysqli, $game_id, $nickname;

    $stmt = $mysqli->prepare("SELECT * FROM games WHERE game_id = ? AND nickname = ?;");
    $stmt->bind_param('is', $game_id, $nickname);
    $stmt->execute();
    if ($stmt->errno) echo "get_my_game_stat SQL failed: (" . $stmt->errno . ") " . $stmt->error;
    $my_result = $stmt->get_result();
    $stmt->close();
    return $my_result->fetch_array();
}

function get_enemy_game_stat()
{
    global $mysqli, $game_id, $nickname;
    
    $stmt = $mysqli->prepare("SELECT * FROM games WHERE game_id = ? AND nickname <> ?;");
    $stmt->bind_param('is', $game_id, $nickname);
    $stmt->execute();
    if ($stmt->errno) echo "get_enemy_game_stat SQL failed: (" . $stmt->errno . ") " . $stmt->error;
    $opponent_result = $stmt->get_result();
    $stmt->close();
    return $opponent_result->fetch_array();
}

function get_users_statistics()
{
    global $mysqli, $nickname, $opponent_game_stat;
    
    $result_arr = array();
    $opponent_nickname = $opponent_game_stat['nickname'];
    
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE nickname = ? OR nickname = ?;");
    $stmt->bind_param('ss', $nickname, $opponent_nickname);
    $stmt->execute();
    if ($stmt->errno) echo "get_users_statistics SQL failed: (" . $stmt->errno . ") " . $stmt->error;
    $result = $stmt->get_result();
    $stmt->close();
    
    $result_arr[] = $result->fetch_array();
    $result_arr[] = $result->fetch_array();
    
    return $result_arr;
}

function get_new_card()
{
    global $mysqli, $game_id, $nickname, $card_location, $my_game_stat;

    $new_card = rand(1,102);

    $stmt = $mysqli->prepare("UPDATE games SET $card_location = ? WHERE game_id = ? AND nickname = ?;");
    $stmt->bind_param('iis', $new_card, $game_id, $nickname);
    $stmt->execute();
    if ($stmt->errno) echo "get_new_card SQL failed: (" . $stmt->errno . ") " . $stmt->error;
    $stmt->close();
    
    $my_game_stat[$card_location] = $new_card;
}

function check_for_win()
{
    global $game_id, $nickname, $my_game_stat, $opponent_game_stat;

    // Resources WIN
    if($my_game_stat['gems'] >= 200  && $my_game_stat['bricks'] >= 200 && $my_game_stat['recruits'] >= 200) end_game(1);
    // Tower WIN
    else if ($my_game_stat['tower'] >= 100) end_game(2);
    // Destory WIN
    else if ($opponent_game_stat['tower'] <= 0 ) end_game(3);

    // Resources LOSE
    else if($opponent_game_stat['gems'] >= 200  && $opponent_game_stat['bricks'] >= 200 && $opponent_game_stat['recruits'] >= 200) end_game(4);
    // Tower LOSE
    else if($opponent_game_stat['tower'] >= 100)  end_game(5);
    // Destory LOSE
    else if($my_game_stat['tower'] <= 0 ) end_game(6);

    // No one win
    else return;
}

function update_resources($resource, $player, $amount)
{
    global $mysqli, $game_id, $nickname, $my_game_stat, $opponent_game_stat;
    switch($player) {

        case 0: //update current player
            if ($resource == "damage")
            {
                $new_wall = $my_game_stat['wall'] + $amount;
                $new_tower = $my_game_stat['tower'];
                if ($new_wall < 0)
                {
                    $new_tower = $new_tower + $new_wall;
                    $new_wall = 0;
                }
                
                $stmt = $mysqli->prepare("UPDATE games SET wall = ?, tower = ? WHERE game_id = ? AND nickname = ?;");
                $stmt->bind_param('iiis', $new_wall, $new_tower, $game_id, $nickname);
                $stmt->execute();
                if ($stmt->errno) echo "update_resources SQL failed: (" . $stmt->errno . ") " . $stmt->error;
                $stmt->close();
                return;
            }
            
            $new_value = max(0, $my_game_stat[$resource] + $amount);
            
            $stmt = $mysqli->prepare("UPDATE games SET $resource = ? WHERE game_id = ? AND nickname = ?;");
            $stmt->bind_param('iis', $new_value, $game_id, $nickname);
            $stmt->execute();
            if ($stmt->errno) echo "update_resources SQL failed: (" . $stmt->errno . ") " . $stmt->error;
            $stmt->close();

            $my_game_stat[$resource] = $new_value;
            //if($resource == 'wall' && $my_game_stat['wall'] + $amount < 0) update_resources('tower', 0, $my_game_stat['wall'] + $amount);
            return;

        case 1: //update enemy
            if ($resource == "damage")
            {
                $new_wall = $opponent_game_stat['wall'] + $amount;
                $new_tower = $opponent_game_stat['tower'];
                if ($new_wall < 0)
                {
                    $new_tower = $new_tower + $new_wall;
                    $new_wall = 0;
                }
                
                
                $stmt = $mysqli->prepare("UPDATE games SET wall = ?, tower = ? WHERE game_id = ? AND nickname='" .$opponent_game_stat['nickname']. "';");
                $stmt->bind_param('iii', $new_wall, $new_tower, $game_id);
                $stmt->execute();
                if ($stmt->errno) echo "update enemy SQL failed: (" . $stmt->errno . ") " . $stmt->error;
                $stmt->close();
                
                return;
            }

            $new_value = max(0, $opponent_game_stat[$resource] + $amount);

            $stmt = $mysqli->prepare("UPDATE games SET $resource = ? WHERE game_id = ? AND nickname = '" .$opponent_game_stat['nickname']. "'");
            $stmt->bind_param('ii', $new_value, $game_id);
            $stmt->execute();
            if ($stmt->errno) echo "update enemy SQL failed: (" . $stmt->errno . ") " . $stmt->error;
            $stmt->close();

            $opponent_game_stat[$resource] = $new_value;
            //if($resource=='wall' && $opponent_game_stat['wall'] + $amount < 0) update_resources('tower', 1, $opponent_game_stat['wall'] + $amount);
            return;

        case 2: //update all
            update_resources($resource, 0, $amount);
            update_resources($resource, 1, $amount);
            return;
    }
}

function resources_cost($resource, $amount)
{
    global $mysqli, $game_id, $nickname, $my_game_stat;

    $new_value = $my_game_stat[$resource] - $amount;

    // If player doesn't have enough resources - exit with 0
    if($new_value < 0) return 0;

    else
    {
        
        $stmt = $mysqli->prepare("UPDATE games SET $resource = ? WHERE game_id = ? AND nickname = ?;");
        $stmt->bind_param('iis', $new_value, $game_id, $nickname);
        $stmt->execute();
        if ($stmt->errno) echo "resources_cost SQL failed: (" . $stmt->errno . ") " . $stmt->error;
        $stmt->close();
        
        $my_game_stat[$resource] = $new_value;
        return 1;
    }
}

function play_card($played_card)
{
    global $my_game_stat, $opponent_game_stat;
    switch ($played_card)
    {
        //**********************************//
        //---------- Pink cards: ----------//

        case 1: //Brick Shortage
            update_resources('bricks', 2, -8);
            return 1;

        case 2: //Lucky Cache
            update_resources('bricks', 0, 2);
            update_resources('gems', 0, 2);
            return 2;

        case 3: //Earthquake
            update_resources('quarry', 2, -1);
            break;

        case 4: //Strip Mine
            update_resources('quarry', 0, -1);
            update_resources('wall', 0, 10);
            update_resources('gems', 0, 5);
            return 1;

        case 5: //Friendly Terrain
            if(resources_cost("gems", 1))
            {
                update_resources('wall', 0, 1);
                return 2;
            }
            return 0;

        case 6: //Rock Garden
            if(resources_cost("gems", 1))
            {
                update_resources('wall', 0, 1);
                update_resources('tower', 0, 1);
                update_resources('recruits', 0, 2);
                return 1;
            }
            return 0;

        case 7: //Work overtime
            if(resources_cost("gems", 2))
            {
                update_resources('wall', 0, 5);
                update_resources('gems', 0, -6);
                return 1;
            }
            return 0;

        case 8: //Basic Wall
            if(resources_cost("gems", 2))
            {
                update_resources('wall', 0, 3);
                return 1;
            }
            return 0;


        case 9: //Innovations
            if(resources_cost("gems", 2))
            {
                update_resources('quarry', 2, 1);
                update_resources('gems', 0, 4);
                return 1;
            }
            return 0;

        case 10: //Miners
            if(resources_cost("gems", 3))
            {
                update_resources('quarry', 0, 1);
                return 1;
            }
            return 0;

        case 11: //Sturdy Wall
            if(resources_cost("gems", 3))
            {
                update_resources('wall', 0, 4);
                return 1;
            }
            return 0;

        case 12: //Foundations
            if(resources_cost("gems",3))
            {
                if($my_game_stat['wall'] == 0)
                {
                    update_resources('wall', 0, 6);
                }
                else
                {
                    update_resources('wall', 0, 3);
                }
                return 1;
            }
            return 0;

        case 13: //Mother Lode
            if(resources_cost("gems", 4))
            {
                if($my_game_stat['quarry'] < $opponent_game_stat['quarry'])
                {
                    update_resources('quarry', 0, 2);
                }
                else
                {
                    update_resources('quarry', 0, 1);
                }
                return 1;
            }
            return 0;

        case 14: //collapse!
            if(resources_cost("gems", 4)) {
                update_resources('quarry', 1, -1);
                return 1;
            }
            return 0;

        case 15: //Copping the Tech
            if(resources_cost("gems", 5))
            {
                if($my_game_stat['quarry'] < $opponent_game_stat['quarry'])
                {
                    update_resources('quarry', 0, ($opponent_game_stat['quarry'] - $my_game_stat['quarry'] ));
                }
                return 1;
            }
            return 0;

        case 16: //Big Wall
            if(resources_cost("gems", 5))
            {
                update_resources('wall', 0, 6);
                return 1;
            }
            return 0;

        case 17: //New Equipment
            if(resources_cost("gems", 5))
            {
                update_resources('quarry', 0, 2);
                return 1;
            }
            return 0;

        case 18: //Flood Water
            if(resources_cost("gems", 6))
            {
                if($my_game_stat['wall'] <= $opponent_game_stat['wall'])
                {
                    update_resources('dungeon', 0, -1);
                    update_resources('tower', 0, -2);
                }

                if($opponent_game_stat['wall'] <= $my_game_stat['wall'])
                {
                    update_resources('dungeon', 1, -1);
                    update_resources('tower', 1, -2);
                }
                return 1;
            }
            return 0;

        case 19: //Dwarven Miners
            if(resources_cost("gems", 7))
            {
                update_resources('wall', 0, 14);
                update_resources('quarry', 0, 1);
                return 1;
            }
            return 0;

        case 20: //Tremors
            if(resources_cost("gems", 7))
            {
                update_resources('wall', 2, -5);
                return 2;
            }
            return 0;

        case 21: //Forced Labor
            if(resources_cost("gems", 7))
            {
                update_resources('wall', 0, 9);
                update_resources('recruits', 0, -5);
                return 1;
            }
            return 0;

        case 22: //Secret Room
            if(resources_cost("gems", 8))
            {
                update_resources('magic', 0, 1);
                return 2;
            }
            return 0;

        case 23: //Reinforced Wall
            if(resources_cost("gems", 8))
            {
                update_resources('wall', 0, 8);
                return 1;
            }
            return 0;

        case 24: //Porticulus
            if(resources_cost("gems", 9))
            {
                update_resources('wall', 0, 5);
                update_resources('dungeon', 0, 1);
                return 1;
            }
            return 0;

        case 25: //Crystal Rocks
            if(resources_cost("gems", 9))
            {
                update_resources('wall', 0, 7);
                update_resources('gems', 0, 7);
                return 1;
            }
            return 0;

        case 26: //Barracks
            if(resources_cost("gems", 10))
            {
                update_resources('recruits', 0, 6);
                update_resources('wall', 0, 6);
                if($my_game_stat['dungeon'] < $opponent_game_stat['dungeon'])
                {
                    update_resources('dungeon', 0, 1);
                }
                return 1;
            }
            return 0;

        case 27: //Harmonic Ore
            if(resources_cost("gems", 11))
            {
                update_resources('wall', 0, 6);
                update_resources('tower', 0, 3);
                return 1;
            }
            return 0;

        case 28: //MondoWall
            if(resources_cost("gems", 13))
            {
                update_resources('wall', 0, 12);
                return 1;
            }
            return 0;

        case 29: //Battlemnets
            if(resources_cost("gems", 14))
            {
                update_resources('wall', 0, 7);
                update_resources('damage', 1, -6);
                return 1;
            }
            return 0;

        case 30: //Focused Designs
            if(resources_cost("gems", 15))
            {
                update_resources('wall', 0, 8);
                update_resources('tower', 0, 5);
                return 1;
            }
            return 0;

        case 31: //Great Wall
            if(resources_cost("gems", 16))
            {
                update_resources('wall', 0, 15);
                return 1;
            }
            return 0;

        case 32: //Shift
            if(resources_cost("gems", 17))
            {
                $tmp = $my_game_stat['wall'] - $opponent_game_stat['wall'];
                update_resources('wall', 0, -$tmp);
                update_resources('wall', 1, $tmp);
                return 1;
            }
            return 0;

        case 33: //Rock Launcher
            if(resources_cost("gems", 18)) 
            {
                update_resources('wall', 0, 6);
                update_resources('damage', 1, -10);
                return 1;
            }
            return 0;

        case 34: //Dragon's Heart
            if(resources_cost("gems" ,24)) 
            {
                update_resources('wall', 0, 20);
                update_resources('tower', 0, 8);
                return 1;
            }
            return 0;

        //**********************************//
        //---------- Blue cards: ----------//


        case 35: //Bag of Baubles
            if($my_game_stat['tower'] < $opponent_game_stat['tower'])
            {
                update_resources('tower', 0, 2);
            }
            else {
                update_resources('tower', 0, 1);
            }
            return 1;

        case 36: //Rainbow
            update_resources('tower', 2, 1);
            update_resources('gems', 0, 3);
            return 1;

        case 37: //Quartz
            if(resources_cost("bricks", 1))
            {
                update_resources('tower', 0, 1);
                return 2;
            }
            return 0;

        case 38: //Smoky Quartz
            if(resources_cost("bricks", 2))
            {
                update_resources('tower', 1, -1);
                return 2;
            }
            return 0;

        case 39: //Amethyst
            if(resources_cost("bricks", 2))
            {
                update_resources('tower', 0, 3);
                return 1;
            }
            return 0;

        case 40: //Prism
            if(resources_cost("bricks", 2))
            {
                return 2;
            }
            return 0;

        case 41: //Gemstone Flaw
            if(resources_cost("bricks", 2))
            {
                update_resources('tower', 1, -3);
                return 1;
            }
            return 0;

        case 42: //Spell Weavers
            if(resources_cost("bricks", 3))
            {
                update_resources('magic', 0, 1);
                return 1;
            }
            return 0;

        case 43: //Ruby
            if(resources_cost("bricks", 3))
            {
                update_resources('tower', 0, 5);
                return 1;
            }
            return 0;

        case 44: //Power Burn
            if(resources_cost("bricks", 3)) 
            {
                update_resources('tower', 0, -5);
                update_resources('magic', 0, 2);
                return 1;
            }
            return 0;

        case 45: //Solar Flare
            if(resources_cost("bricks", 4)) 
            {
                update_resources('tower', 0, 2);
                update_resources('tower', 1, -2);
                return 1;
            }
            return 0;

        case 46: //Gem Spear
            if(resources_cost("bricks", 4)) 
            {
                update_resources('tower', 1, -5);
                return 1;
            }
            return 0;

        case 47: //Quarry's Help
            if(resources_cost("bricks", 4)) 
            {
                update_resources('tower', 0, 7);
                update_resources('bricks', 0, -10);
                return 1;
            }
            return 0;

        case 48: //Lodestone
            if(resources_cost("bricks", 5)) 
            {
                update_resources('tower', 0, 3); // TODO: !!!!!!!!!!!!!!!! can't be discarded!!!!!!!!
                return 1;
            }
            return 0;

        case 49: //Discord
            if(resources_cost("bricks", 5)) 
            {
                update_resources('tower', 2, -7);
                update_resources('magic', 2, -1);
                return 1;
            }
            return 0;

        case 50: //Apprentice
            if(resources_cost("bricks",5)) 
            {
                update_resources('tower', 0, 4);
                update_resources('recruits', 0, -3);
                update_resources('tower', 1, -2);
                return 1;
            }
            return 0;

        case 51: //Crystal Matrix
            if(resources_cost("bricks", 6)) 
            {
                update_resources('magic', 0, 2);
                update_resources('tower', 0, 3);
                update_resources('tower', 1, 1);
                return 1;
            }
            return 0;

        case 52: //Emerald
            if(resources_cost("bricks", 6)) 
            {
                update_resources('tower', 0, 8);
                return 1;
            }
            return 0;

        case 53: //Harmonic Vibe
            if(resources_cost("bricks", 7)) 
            {
                update_resources('magic', 0, 1);
                update_resources('tower', 0, 3);
                update_resources('wall', 0, 3);
                return 1;
            }
            return 0;

        case 54: //Parity
            if(resources_cost("bricks", 7)) 
            {
                if($my_game_stat['magic'] < $opponent_game_stat['magic']) 
                {
                    update_resources('magic', 0, $opponent_game_stat['magic'] - $my_game_stat['magic'] );
                }
                else 
                {
                    update_resources('magic', 1, $my_game_stat['magic'] - $opponent_game_stat['magic'] );
                }
                return 1;
            }
            return 0;

        case 55: //Crumblestone
            if(resources_cost("bricks", 7)) 
            {
                update_resources('tower', 0, 5);
                update_resources('bricks', 1, -6);
                return 1;
            }
            return 0;

        case 56: //Shatterer
            if(resources_cost("bricks", 8)) 
            {
                update_resources('magic', 0, -1);
                update_resources('tower', 1, -9);
                return 1;
            }
            return 0;

        case 57: //Crystallize
            if(resources_cost("bricks", 8)) 
            {
                update_resources('tower', 0, 11);
                update_resources('wall', 0, -6);
                return 1;
            }
            return 0;

        case 58: //Pearl Of Wisdom
            if(resources_cost("bricks", 9)) 
            {
                update_resources('tower', 0, 5);
                update_resources('magic', 0, 1);
                return 1;
            }
            return 0;

        case 59: //Sapphire
            if(resources_cost("bricks", 10)) 
            {
                update_resources('tower', 0, 11);
                return 1;
            }
            return 0;

        case 60: //Lightning Shard
            if(resources_cost("bricks", 11)) 
            {
                if($my_game_stat['tower'] > $opponent_game_stat['wall']) 
                {
                    update_resources('tower', 1, -8);
                }
                else 
                {
                    update_resources('damage', 1, -8);
                }
                return 1;
            }
            return 0;

        case 61: //Crystal Shield
            if(resources_cost("bricks", 12)) 
            {
                update_resources('tower', 0, 8);
                update_resources('wall', 0, 3);
                return 1;
            }
            return 0;

        case 62: //Fire Ruby
            if(resources_cost("bricks", 12)) 
            {
                update_resources('tower', 0, 6);
                update_resources('tower', 1, -4);
                return 1;
            }
            return 0;

        case 63: //Empathy Gem
            if(resources_cost("bricks", 14)) 
            {
                update_resources('tower', 0, 8);
                update_resources('dungeon', 0, 1);
                return 1;
            }
            return 0;

        case 64: //Sanctuary
            if(resources_cost("bricks", 15)) 
            {
                update_resources('tower', 0, 10);
                update_resources('wall', 0, 5);
                update_resources('recruits', 0, 5);
                return 1;
            }
            return 0;

        case 65: //Diamond
            if(resources_cost("bricks", 16)) 
            {
                update_resources('tower', 0, 15);
                return 1;
            }
            return 0;

        case 66: //Lava Jewel
            if(resources_cost("bricks", 17)) 
            {
                update_resources('tower', 0, 12);
                update_resources('damage', 1, -6);
                return 1;
            }
            return 0;

        case 67: //Phase Jewel
            if(resources_cost("bricks", 18)) 
            {
                update_resources('tower', 0, 13);
                update_resources('recruits', 0, 6);
                update_resources('bricks', 0, 6);
                return 1;
            }
            return 0;

        case 68: //Dragon's Eye
            if(resources_cost("bricks", 21)) 
            {
                update_resources('tower', 0, 20);
                return 1;
            }
            return 0;

        //***********************************//
        //---------- Green cards: ----------//

        case 69: // MadCowDisease
            update_resources('recruits', 2, -6);
            return 1;

        case 70: //Full Moon
            update_resources('dungeon', 2, 1);
            update_resources('recruits', 0, 3);
            return 1;

        case 71: //Faerie
            if(resources_cost("recruits", 1)) 
            {
                update_resources('damage', 1, -2);
                return 2;
            }
            return 0;

        case 72: //Moody Goblins
            if(resources_cost("recruits", 1)) 
            {
                update_resources('damage', 1, -4);
                update_resources('gems', 0, -3);
                return 1;
            }
            return 0;

        case 73: //Elven Scout
            if(resources_cost("recruits", 2)) 
            {
                return 2;
            }
            return 0;

        case 74: //Spearman
            if(resources_cost("recruits", 2)) 
            {
                if($my_game_stat['wall'] > $opponent_game_stat['wall']) 
                {
                    update_resources('damage', 1, -3);
                }
                else 
                {
                    update_resources('damage', 1, -2);
                }
                return 1;
            }
            return 0;

        case 75: //Gnome
            if(resources_cost("recruits", 2)) 
            {
                update_resources('damage', 1, -3);
                update_resources('gems', 0, 1);
                return 1;
            }
            return 0;

        case 76: //Minotaur
            if(resources_cost("recruits", 3)) 
            {
                update_resources('dungeon', 0, 1);
                return 1;
            }
            return 0;

        case 77: //Goblin Mob
            if(resources_cost("recruits", 3)) 
            {
                update_resources('damage', 1, -6);
                update_resources('damage', 0, -3);
                return 1;
            }
            return 0;

        case 78: //Orc
            if(resources_cost("recruits", 3)) 
            {
                update_resources('damage', 1, -5);
                return 1;
            }
            return 0;

        case 79: //Goblin Archers
            if(resources_cost("recruits", 4)) 
            {
                update_resources('tower', 1, -3);
                update_resources('damage', 0, -1);
                return 1;
            }
            return 0;

        case 80: //Berserker
            if(resources_cost("recruits", 4)) 
            {
                update_resources('damage', 1, -8);
                update_resources('tower', 0, -3);
                return 1;
            }
            return 0;

        case 81: //Dwarves
            if(resources_cost("recruits", 5)) 
            {
                update_resources('damage', 1, -4);
                update_resources('wall', 0, 3);
                return 1;
            }
            return 0;

        case 82: //Slasher
            if(resources_cost("recruits", 5)) 
            {
                update_resources('damage', 1, -6);
                return 1;
            }
            return 0;

        case 83: //Imp
            if(resources_cost("recruits", 5)) 
            {
                update_resources('damage', 1, -6);
                update_resources('bricks', 2, -5);
                update_resources('gems', 2, -5);
                update_resources('recruits', 2, -5);
                return 1;
            }
            return 0;

        case 84: //Shadow Faerie
            if(resources_cost("recruits", 6)) 
            {
                update_resources('tower', 1, -2);
                return 2;
            }
            return 0;

        case 85: //Little Snakes
            if(resources_cost("recruits", 6)) 
            {
                update_resources('tower', 1, -4);
                return 1;
            }
            return 0;

        case 86: //Ogre
            if(resources_cost("recruits", 6)) 
            {
                update_resources('damage', 1, -7);
                return 1;
            }
            return 0;

        case 87: //Rabid Sheep
            if(resources_cost("recruits", 6)) 
            {
                update_resources('damage', 1, -6);
                update_resources('recruits', 1, -3);
                return 1;
            }
            return 0;

        case 88: //Troll Trainer
            if(resources_cost("recruits", 7)) 
            {
                update_resources('dungeon', 0, 2);
                return 1;
            }
            return 0;

        case 89: //Tower Gremlin
            if(resources_cost("recruits", 8)) 
            {
                update_resources('damage', 1, -2);
                update_resources('wall', 0, 4);
                update_resources('tower', 0, 2);
                return 1;
            }
            return 0;

        case 90: //Spizzer
            if(resources_cost("recruits", 8)) 
            {
                if($opponent_game_stat['wall'] == 0) 
                {
                    update_resources('damage', 1, -10);
                }
                else 
                {
                    update_resources('damage', 1, -6);
                }
                return 1;
            }
            return 0;

        case 91: //Werewolf
            if(resources_cost("recruits", 9)) 
            {
                update_resources('damage', 1, -9);
                return 1;
            }
            return 0;

        case 92: //Unicorn
            if(resources_cost("recruits", 9)) 
            {
                if($my_game_stat['magic'] > $opponent_game_stat['magic']) 
                {
                    update_resources('damage', 1, -12);
                }
                else
                {
                    update_resources('damage', 1, -8);
                }
                return 1;
            }
            return 0;

        case 93: //Elven Archers
            if(resources_cost("recruits", 10)) 
            {
                if($my_game_stat['wall'] > $opponent_game_stat['wall']) 
                {
                    update_resources('tower', 1, -6);
                }
                else {
                    update_resources('damage', 1, -6);
                }
                return 1;
            }
            return 0;

        case 94: //Corrosion Clouds
            if(resources_cost("recruits", 11))
            {
                if($opponent_game_stat['wall'] > 0) 
                {
                    update_resources('damage', 1, -10);
                }
                else {
                    update_resources('damage', 1, -7);
                }
                return 1;
            }
            return 0;

        case 95: //Rock Stompers
            if(resources_cost("recruits", 11)) 
            {
                update_resources('damage', 1, -8);
                update_resources('quarry', 1, -1);
                return 1;
            }
            return 0;

        case 96: //Thief
            if(resources_cost("recruits", 12)) 
            {
                update_resources('gems', 1, -10);
                update_resources('bricks', 1, -5);
                update_resources('gems', 0, 5);
                update_resources('bricks', 0, 3); // TODO: currect ???????????????
                return 1;
            }
            return 0;

        case 97: //Warlord
            if(resources_cost("recruits", 13)) 
            {
                update_resources('damage', 1, -13);
                update_resources('gems', 0, -3);
                return 1;
            }
            return 0;

        case 98: //Succubus
            if(resources_cost("recruits", 14)) 
            {
                update_resources('tower', 1, -5);
                update_resources('recruits', 1, -8);
                return 1;
            }
            return 0;

        case 99: //Stone Giant
            if(resources_cost("recruits", 15)) 
            {
                update_resources('damage', 1, -10);
                update_resources('wall', 0, 4);
                return 1;
            }
            return 0;

        case 100: //Vampire
            if(resources_cost("recruits", 17)) 
            {
                update_resources('damage', 1, -10);
                update_resources('recruits', 1, -5);
                update_resources('dungeon', 1, -1);
                return 1;
            }
            return 0;

        case 101: //Pegasus Lancer
            if(resources_cost("recruits", 18)) 
            {
                update_resources('tower', 1, -12);
                return 1;
            }
            return 0;

        case 102: //Dragon
            if(resources_cost("recruits", 25)) 
            {
                update_resources('damage', 1, -20);
                update_resources('gems', 1, -10);
                update_resources('dungeon', 1, -1);
                return 1;
            }
            return 0;

        default: //dicard card_id
            return 4;

    } //end switch
} // end play_card


//********************* MAIN*************************//
if ($card_location == "surrender" | $card_location == "surrender_id") end_game(0);
else {
    $played_card = $my_game_stat[$card_location];
    main();
}

$mysqli->close();
?>
