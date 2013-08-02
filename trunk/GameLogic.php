<?php
// Validate input to avoid attacks
preg_match('/^[a-zA-Z0-9_]+$/',$_REQUEST["card_location"]) ? $card_location = $_REQUEST["card_location"] : exit('XSS is detected!');

// Start the session and get current user information
session_start();
$game_id = $_SESSION['game_id'];
$nickname = $_SESSION['nickname'];

// Connect to the database of users and games
$mysqli = new mysqli("localhost", "root", "12345", "test");
// Check connection and echo if error on connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// We get the table column in which the played card value is so we need to get the actual value
function get_played_card_num(){
    global $mysqli, $game_id, $nickname, $card_location;
    $my_result = $mysqli->query("SELECT * FROM games WHERE game_id = '$game_id' AND nickname = '$nickname'");
    if (!$my_result){
        echo "get_played_card_num SQL failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    $row = $my_result->fetch_array();
    return $row[$card_location];
}

function get_my_game_stat()
{
    global $mysqli, $game_id, $nickname;
    $my_result = $mysqli->query("SELECT * FROM games WHERE game_id = '$game_id' AND nickname = '$nickname'");
    if (!$my_result){
        echo "get_my_game_stat SQL failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    return $my_result->fetch_array();
}

function get_enemy_game_stat()
{
    global $mysqli, $game_id, $nickname;
    $enemy_result = $mysqli->query("SELECT * FROM games WHERE game_id = '$game_id' AND nickname <> '$nickname'");
    if (!$enemy_result){
        echo "get_enemy_game_stat SQL failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    return $enemy_result->fetch_array();
}

function get_new_card()
{
    global $mysqli, $game_id, $nickname, $card_location;

    $my_result = $mysqli->query("UPDATE games SET $card_location = " .rand(1,102). " WHERE game_id = '$game_id' AND nickname = '$nickname'");
    if (!$my_result){
        echo "get_new_card SQL failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
}

function check_for_win()
{
    global $mysqli, $game_id, $nickname;
    $my_row = get_my_game_stat();
    $enemy_row = get_enemy_game_stat();

    if( ($my_row['gems'] >= 200  && $my_row['bricks'] >= 200 && $my_row['recruits'] >= 200
            || $my_row['tower'] >= 100
            || $enemy_row['tower'] <= 0 )
        && ($enemy_row['gems'] >= 200  && $enemy_row['bricks'] >= 200 && $enemy_row['recruits'] >= 200
            || $enemy_row['tower'] >= 100
            || $my_row['tower'] <= 0 ) ) {
        $query = "UPDATE games SET game_end_status = '3' WHERE game_id = '$game_id';";
    }

    else if($my_row['gems'] >= 200  && $my_row['bricks'] >= 200 && $my_row['recruits'] >= 200
        || $my_row['tower'] >= 100
        || $enemy_row['tower'] <= 0 ){

        $query = "UPDATE games SET game_end_status = '2' WHERE game_id = '$game_id' AND nickname = '$nickname';
                    UPDATE games SET game_end_status = '1' WHERE game_id = '$game_id' AND nickname != '$nickname';";
    }

    else if(($enemy_row['gems'] >= 200  && $enemy_row['bricks'] >= 200 && $enemy_row['recruits'] >= 200
        || $enemy_row['tower'] >= 100
        || $my_row['tower'] <= 0 )) {

        $query = "UPDATE games SET game_end_status = '1' WHERE game_id = '$game_id' AND nickname = '$nickname';
                    UPDATE games SET game_end_status = '2' WHERE game_id = '$game_id' AND nickname != '$nickname';";
    }

    else return;

    // Execute Query and save game results
    $my_result = $mysqli->query($query);
    if (!$my_result){
        echo "check_for_win SQL failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    
    // Exit with GameOver to mark game end
    exit("GameOver");
} // 0 if no win, 1 if I win, 2 if enemy wins, 3 if it's a tie

function update_resources($resource, $player, $amount)
{
    global $mysqli, $game_id, $nickname;
    $my_row = get_my_game_stat();
    $enemy_row = get_enemy_game_stat();
    switch($player) {

        case 0: //update current player
            $result = $mysqli->query("UPDATE games SET $resource = '".max(0, $my_row[$resource] + $amount)."'
                    WHERE game_id='".$game_id."' AND nickname='".$nickname."'");
            if (!$result){
                echo "update current player SQL failed: (" . $mysqli->errno . ") " . $mysqli->error;
            }
            if($resource == 'wall' && $my_row['wall'] + $amount < 0) {
                update_resources('tower', 0, $my_row['wall'] + $amount);
            }
            return;

        case 1: //update enemy
            $result = $mysqli->query("UPDATE games SET $resource ='".max(0, $enemy_row[$resource] + $amount)."'
                    WHERE game_id='".$enemy_row['game_id']."' AND nickname='".$enemy_row['nickname']."'");
            if (!$result){
                echo "update enemy SQL failed: (" . $mysqli->errno . ") " . $mysqli->error;
            }
            if($resource=='wall' && $enemy_row['wall'] + $amount < 0) {
                update_resources('tower', 1, $enemy_row['wall'] + $amount);
            }
            return;

        case 2: //update all
            update_resources($resource, 0, $amount);
            update_resources($resource, 1, $amount);
            return;
    }
}

function resources_cost($resource, $amount)
{
    global $mysqli, $game_id, $nickname;
    // Get players game stat
    $my_row = get_my_game_stat();

    // If player doesn't have enough resources - exit with 0
    if($my_row[$resource] - $amount < 0) {
        return 0;
    }

    else {
        $result = $mysqli->query("UPDATE games SET $resource ='".($my_row[$resource] - $amount)."'
                    WHERE game_id='".$my_row['game_id']."' AND nickname='".$my_row['nickname']."'");
        if (!$result){
            echo "Update resources_cost SQL failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }

        return 1;
    }
}

function get_resource($resource, $player)
{
    global $mysqli, $game_id, $nickname;
    if($player) {
        $enemy_row = get_enemy_game_stat();
        return $enemy_row[$resource];
    }
    $my_row = get_my_game_stat();
    return $my_row[$resource];
}

function play_card($played_card)
{
    global $mysqli, $game_id, $nickname;
    switch ($played_card)
    {

        //**********************************//
        //---------- Pink cards: ----------//

        case 1: //Brick Shortage
            update_resources('bricks',2,-8);
            return 1;

        case 2: //Lucky Cache
            update_resources('bricks',0,2);
            update_resources('gems',0,2);
            return 2;

        case 3: //Earthquake

            break;

        case 4: //Strip Mine
            update_resources('quarry',2,-1);
            return 1;

        case 5: //Friendly Terrain
            if(resources_cost("gems",1))
            {
                update_resources('wall',0,1);
                return 2;
            }
            return 0;

        case 6: //Rock Garden
            if(resources_cost("gems",1))
            {
                update_resources('wall',0,1);
                update_resources('tower',0,1);
                update_resources('recruits',0,2);
                return 1;
            }
            return 0;

        case 7: //Work overtime
            if(resources_cost("gems",2))
            {
                update_resources('wall',0,5);
                update_resources('gems',0,-6);
                return 1;
            }
            return 0;

        case 8: //Basic Wall
            if(resources_cost("gems",2))
            {
                update_resources('wall',0,3);
                return 1;
            }
            return 0;


        case 9: //Innovations
            if(resources_cost("gems",2))
            {
                update_resources('quarry',2,1);
                update_resources('gems',0,4);
                return 1;
            }
            return 0;

        case 10: //Miners
            if(resources_cost("gems",3))
            {
                update_resources('quarry',0,1);
                return 1;
            }
            return 0;

        case 11: //Sturdy Wall
            if(resources_cost("gems",3))
            {
                update_resources('wall',0,4);
                return 1;
            }
            return 0;

        case 12: //Foundations
            if(resources_cost("gems",3)) {
                if(get_update_resources('wall',0)==0) {
                    update_resources('wall',0,6);
                }
                else {
                    update_resources('wall',0,3);
                }
                return 1;
            }
            return 0;

        case 13: //Mother Lode
            if(resources_cost("gems",4)) {
                if(get_resource('quarry',0) < get_resource('quarry',1)) {
                    update_resources('quarry',0,2);
                }
                else {
                    update_resources('quarry',0,1);
                }
                return 1;
            }
            return 0;

        case 14: //collapse!
            if(resources_cost("gems",4)) {
                update_resources('quarry',1,-1);
                return 1;
            }
            return 0;

        case 15: //Copping the Tech
            if(resources_cost("gems",5)) {
                if(get_resource('quarry',0)<get_resource('quarry',1)) {
                    update_resources('quarry',0,(get_resource('quarry',1)-get_resource('quarry',0)));
                }
                return 1;
            }
            return 0;

        case 16: //Big Wall
            if(resources_cost("gems",5)) {
                update_resources('wall',0,6);
                return 1;
            }
            return 0;

        case 17: //New Equipment
            if(resources_cost("gems",5)) {
                update_resources('quarry',0,2);
                return 1;
            }
            return 0;

        case 18: //Flood Water
            if(resources_cost("gems",6)) {
                if(get_resource('wall',0)<=get_resource('wall',1)) {
                    update_resources('dungeon',0,-1);
                    update_resources('tower',0,-2);
                }
                if(get_resource('wall',1)<=get_resource('wall',2)) {
                    update_resources('dungeon',1,-1);
                    update_resources('tower',1,-2);
                }
                return 1;
            }
            return 0;

        case 19: //Dwarven Miners
            if(resources_cost("gems",7)) {
                update_resources('wall',0,14);
                update_resources('quarry',0,1);
                return 1;
            }
            return 0;

        case 20: //Tremors
            if(resources_cost("gems",7)) {
                update_resources('wall',2,-5);
                return 2;
            }
            return 0;

        case 21: //Forced Labor
            if(resources_cost("gems",7)) {
                update_resources('wall',0,9);
                update_resources('recruits',0,-5);
                return 1;
            }
            return 0;

        case 22: //Secret Room
            if(resources_cost("gems",8)) {
                update_resources('magic',0,1);
                return 2;
            }
            return 0;

        case 23: //Reinforced Wall
            if(resources_cost("gems",8)) {
                update_resources('wall',0,8);
                return 1;
            }
            return 0;

        case 24: //Porticulus
            if(resources_cost("gems",9)) {
                update_resources('wall',0,5);
                update_resources('dungeon',0,1);
                return 1;
            }
            return 0;

        case 25: //Crystal Rocks
            if(resources_cost("gems",9)) {
                update_resources('wall',0,7);
                update_resources('gems',0,7);
                return 1;
            }
            return 0;

        case 26: //Barracks
            if(resources_cost("gems",10)) {
                update_resources('recruits',0,6);
                update_resources('wall',0,6);
                if(get_resource('dungeon',0)<get_resource('dungeon',1)) {
                    update_resources('dungeon',0,1);
                }
                return 1;
            }
            return 0;

        case 27: //Harmonic Ore
            if(resources_cost("gems",11)) {
                update_resources('wall',0,6);
                update_resources('tower',0,3);
                return 1;
            }
            return 0;

        case 28: //MondoWall
            if(resources_cost("gems",13)) {
                update_resources('wall',0,12);
                return 1;
            }
            return 0;

        case 29: //Battlemnets
            if(resources_cost("gems",14)) {
                update_resources('wall',0,7);
                update_resources('wall',1,-6);
                return 1;
            }
            return 0;

        case 30: //Focused Designs
            if(resources_cost("gems",15)) {
                update_resources('wall',0,8);
                update_resources('tower',0,5);
                return 1;
            }
            return 0;

        case 31: //Great Wall
            if(resources_cost("gems",16)) {
                update_resources('wall',0,15);
                return 1;
            }
            return 0;

        case 32: //Shift
            if(resources_cost("gems",17)) {
                $tmp = get_resource('wall',0) - get_resource('wall',1);
                update_resources('wall',0,-$tmp);
                update_resources('wall',1,$tmp);
                return 1;
            }
            return 0;

        case 33: //Rock Launcher
            if(resources_cost("gems",18)) {
                update_resources('wall',0,6);
                update_resources('wall',1,-10);
                return 1;
            }
            return 0;

        case 34: //Dragon's Heart
            if(resources_cost("gems",24)) {
                update_resources('wall', 0, 20);
                update_resources('tower', 0, 8);
                return 1;
            }
            return 0;

        //**********************************//
        //---------- Blue cards: ----------//


        case 35: //Bag of Baubles
            if(get_resource('tower',0)<get_resource('tower',1)) {
                update_resources('tower',0,2);
            }
            else {
                update_resources('tower',0,1);
            }
            return 1;

        case 36: //Rainbow
            update_resources('tower',2,1);
            update_resources('gems',0,3);
            return 1;

        case 37: //Quartz
            if(resources_cost("bricks",1)) {
                update_resources('tower',0,1);
                return 2;
            }
            return 0;

        case 38: //Smoky Quartz
            if(resources_cost("bricks",2)) {
                update_resources('tower',1,-1);
                return 2;
            }
            return 0;

        case 39: //Amethyst
            if(resources_cost("bricks",2)) {
                update_resources('tower',0,3);
                return 1;
            }
            return 0;

        case 40: //Prism
            if(resources_cost("bricks",2)) {
                return 2;
            }
            return 0;

        case 41: //Gemstone Flaw
            if(resources_cost("bricks",2)) {
                update_resources('tower',1,-3);
                return 1;
            }
            return 0;

        case 42: //Spell Weavers
            if(resources_cost("bricks",3)) {
                update_resources('magic',0,1);
                return 1;
            }
            return 0;

        case 43: //Ruby
            if(resources_cost("bricks",3)) {
                update_resources('tower',0,5);
                return 1;
            }
            return 0;

        case 44: //Power Burn
            if(resources_cost("bricks",3)) {
                update_resources('tower',0,-5);
                update_resources('magic',0,2);
                return 1;
            }
            return 0;

        case 45: //Solar Flare
            if(resources_cost("bricks",4)) {
                update_resources('tower',0,2);
                update_resources('tower',1,-2);
                return 1;
            }
            return 0;

        case 46: //Gem Spear
            if(resources_cost("bricks",4)) {
                update_resources('tower',1,-5);
                return 1;
            }
            return 0;

        case 47: //Quarry's Help
            if(resources_cost("bricks",4)) {
                update_resources('tower',0,7);
                update_resources('bricks',0,-10);
                return 1;
            }
            return 0;

        case 48: //Lodestone
            if(resources_cost("bricks",5)) {
                update_resources('tower',0,3); //TODO !!!!!!!!!!!!!!!! can't be discarded!!!!!!!!
                return 1;
            }
            return 0;

        case 49: //Discord
            if(resources_cost("bricks",5)) {
                update_resources('tower',2,-7);
                update_resources('magic',2,-1);
                return 1;
            }
            return 0;

        case 50: //Apprentice
            if(resources_cost("bricks",5)) {
                update_resources('tower',0,4);
                update_resources('recruits',0,-3);
                update_resources('tower',1,-2);
                return 1;
            }
            return 0;

        case 51: //Crystal Matrix
            if(resources_cost("bricks",6)) {
                update_resources('magic',0,2);
                update_resources('tower',0,3);
                update_resources('tower',1,1);
                return 1;
            }
            return 0;

        case 52: //Emerald
            if(resources_cost("bricks",6)) {
                update_resources('tower',0,8);
                return 1;
            }
            return 0;

        case 53: //Harmonic Vibe
            if(resources_cost("bricks",7)) {
                update_resources('magic',0,1);
                update_resources('tower',0,3);
                update_resources('wall',0,3);
                return 1;
            }
            return 0;

        case 54: //Parity
            if(resources_cost("bricks",7)) {
                if(get_resource('magic',0)<get_resource('magic',1)) {
                    update_resources('magic',0, get_resource('magic',1) - get_resource('magic',0) );
                }
                else {
                    update_resources('magic',1, get_resource('magic',0) - get_resource('magic',1) );
                }
                return 1;
            }
            return 0;

        case 55: //Crumblestone
            if(resources_cost("bricks",7)) {
                update_resources('tower',0,5);
                update_resources('bricks',1,-6);
                return 1;
            }
            return 0;

        case 56: //Shatterer
            if(resources_cost("bricks",8)) {
                update_resources('magic',0,-1);
                update_resources('tower',1,-9);
                return 1;
            }
            return 0;

        case 57: //Crystallize
            if(resources_cost("bricks",8)) {
                update_resources('tower',0,11);
                update_resources('wall',0,-6);
                return 1;
            }
            return 0;

        case 58: //Pearl Of Wisdom
            if(resources_cost("bricks",9)) {
                update_resources('tower',0,5);
                update_resources('magic',0,1);
                return 1;
            }
            return 0;

        case 59: //Sapphire
            if(resources_cost("bricks",10)) {
                update_resources('tower',0,11);
                return 1;
            }
            return 0;

        case 60: //Lightning Shard
            if(resources_cost("bricks",11)) {
                if(get_resource('tower',0) > get_resource('wall',1)) {
                    update_resources('tower',1,-8);
                }
                else {
                    update_resources('wall',1,-8);
                }
                return 1;
            }
            return 0;

        case 61: //Crystal Shield
            if(resources_cost("bricks",12)) {
                update_resources('tower',0,8);
                update_resources('wall',0,3);
                return 1;
            }
            return 0;

        case 62: //Fire Ruby
            if(resources_cost("bricks",12)) {
                update_resources('tower',0,6);
                update_resources('tower',1,-4);
                return 1;
            }
            return 0;

        case 63: //Empathy Gem
            if(resources_cost("bricks",14)) {
                update_resources('tower',0,8);
                update_resources('dungeon',0,1);
                return 1;
            }
            return 0;

        case 64: //Sanctuary
            if(resources_cost("bricks",15)) {
                update_resources('tower',0,10);
                update_resources('wall',0,5);
                update_resources('recruits',0,5);
                return 1;
            }
            return 0;

        case 65: //Diamond
            if(resources_cost("bricks",16)) {
                update_resources('tower',0,15);
                return 1;
            }
            return 0;

        case 66: //Lava Jewel
            if(resources_cost("bricks",17)) {
                update_resources('tower',0,12);
                update_resources('wall',1,-6);
                return 1;
            }
            return 0;

        case 67: //Phase Jewel
            if(resources_cost("bricks",18)) {
                update_resources('tower',0,13);
                update_resources('recruits',0,6);
                update_resources('bricks',0,6);
                return 1;
            }
            return 0;

        case 68: //Dragon's Eye
            if(resources_cost("bricks",21)) {
                update_resources('tower',0,20);
                return 1;
            }
            return 0;

        //***********************************//
        //---------- Green cards: ----------//

        case 69: // MadCowDisease
            update_resources('recruits',2,-6);
            return 1;

        case 70: //Full Moon
            update_resources('dungeon',2,1);
            update_resources('recruits',0,3);
            return 1;

        case 71: //Faerie
            if(resources_cost("recruits",1)) {
                update_resources('wall',1,-2);
                return 2;
            }
            return 0;

        case 72: //Moody Goblins
            if(resources_cost("recruits",1)) {
                update_resources('wall',1,-4);
                update_resources('gems',0,-3);
                return 1;
            }
            return 0;

        case 73: //Elven Scout
            if(resources_cost("recruits",2)) {
                return 2;
            }
            return 0;

        case 74: //Spearman
            if(resources_cost("recruits",2)) {
                if(get_resource('wall',0)>get_resource('wall',1)) {
                    update_resources('wall',1,-3);
                }
                else {
                    update_resources('wall',1,-2);
                }
                return 1;
            }
            return 0;

        case 75: //Gnome
            if(resources_cost("recruits",2)) {
                update_resources('wall',1,-3);
                update_resources('gems',0,1);
                return 1;
            }
            return 0;

        case 76: //Minotaur
            if(resources_cost("recruits",3)) {
                update_resources('dungeon',0,1);
                return 1;
            }
            return 0;

        case 77: //Goblin Mob
            if(resources_cost("recruits",3)) {
                update_resources('wall',1,-6);
                update_resources('wall',0,-3);
                return 1;
            }
            return 0;

        case 78: //Orc
            if(resources_cost("recruits",3)) {
                update_resources('wall',1,-5);
                return 1;
            }
            return 0;

        case 79: //Goblin Archers
            if(resources_cost("recruits",4)) {
                update_resources('tower',1,-3);
                update_resources('wall',0,-1);
                return 1;
            }
            return 0;

        case 80: //Berserker
            if(resources_cost("recruits",4)) {
                update_resources('wall',1,-8);
                update_resources('tower',0,-3);
                return 1;
            }
            return 0;

        case 81: //Dwarves
            if(resources_cost("recruits",5)) {
                update_resources('wall',1,-4);
                update_resources('wall',0,3);
                return 1;
            }
            return 0;

        case 82: //Slasher
            if(resources_cost("recruits",5)) {
                update_resources('wall',1,-6);
                return 1;
            }
            return 0;

        case 83: //Imp
            if(resources_cost("recruits",5)) {
                update_resources('wall',1,-6);
                update_resources('bricks',2,-5);
                update_resources('gems',2,-5);
                update_resources('recruits',2,-5);
                return 1;
            }
            return 0;

        case 84: //Shadow Faerie
            if(resources_cost("recruits",6)) {
                update_resources('tower',1,-2);
                return 2;
            }
            return 0;

        case 85: //Little Snakes
            if(resources_cost("recruits",6)) {
                update_resources('tower',1,-4);
                return 1;
            }
            return 0;

        case 86: //Ogre
            if(resources_cost("recruits",6)) {
                update_resources('wall',1,-7);
                return 1;
            }
            return 0;

        case 87: //Rabid Sheep
            if(resources_cost("recruits",6)) {
                update_resources('wall',1,-6);
                update_resources('recruits',1,-3);
                return 1;
            }
            return 0;

        case 88: //Troll Trainer
            if(resources_cost("recruits",7)) {
                update_resources('dungeon',0,2);
                return 1;
            }
            return 0;

        case 89: //Tower Gremlin
            if(resources_cost("recruits",8)) {
                update_resources('wall',1,-2);
                update_resources('wall',0,4);
                update_resources('tower',0,2);
                return 1;
            }
            return 0;

        case 90: //Spizzer
            if(resources_cost("recruits",8)) {
                if(get_resource('wall',1)==0) {
                    update_resources('wall',1,-10);
                }
                else {
                    update_resources('wall',1,-6);
                }
                return 1;
            }
            return 0;

        case 91: //Werewolf
            if(resources_cost("recruits",9)) {
                update_resources('wall',1,-9);
                return 1;
            }
            return 0;

        case 92: //Unicorn
            if(resources_cost("recruits",9)) {
                if(get_resource('magic',0) > get_resource('magic',1)) {
                    update_resources('wall',1,-12);
                }
                else {
                    update_resources('wall',1,-8);
                }
                return 1;
            }
            return 0;

        case 93: //Elven Archers
            if(resources_cost("recruits",10)) {
                if(get_resource('wall',0)>get_resource('wall',1)) {
                    update_resources('tower',1,-6);
                }
                else {
                    update_resources('wall',1,-6);
                }
                return 1;
            }
            return 0;

        case 94: //Corrosion Clouds
            if(resources_cost("recruits",11))
            {
                if(get_resource('wall',1)>0) {
                    update_resources('wall',1,-10);
                }
                else {
                    update_resources('wall',1,-7);
                }
                return 1;
            }
            return 0;

        case 95: //Rock Stompers
            if(resources_cost("recruits",11)) {
                update_resources('wall',1,-8);
                update_resources('quarry',1,-1);
                return 1;
            }
            return 0;

        case 96: //Thief
            if(resources_cost("recruits",12)) {
                update_resources('gems',1,-10);
                update_resources('bricks',1,-5);
                update_resources('gems',0,5);
                update_resources('bricks',0,3); // TODO: currect ???????????????
                return 1;
            }
            return 0;

        case 97: //Warlord
            if(resources_cost("recruits",13)) {
                update_resources('wall',1,-13);
                update_resources('gems',0,-3);
                return 1;
            }
            return 0;

        case 98: //Succubus
            if(resources_cost("recruits",14)) {
                update_resources('tower',1,-5);
                update_resources('recruits',1,-8);
                return 1;
            }
            return 0;

        case 99: //Stone Giant
            if(resources_cost("recruits",15)) {
                update_resources('wall',1,-10);
                update_resources('wall',0,4);
                return 1;
            }
            return 0;

        case 100: //Vampire
            if(resources_cost("recruits",17)) {
                update_resources('wall',1,-10);
                update_resources('recruits',1,-5);
                update_resources('dungeon',1,-1);
                return 1;
            }
            return 0;

        case 101: //Pegasus Lancer
            if(resources_cost("recruits",18)) {
                update_resources('tower',1,-12);
                return 1;
            }
            return 0;

        case 102: //Dragon
            if(resources_cost("recruits",25)) {
                update_resources('wall',1,-20);
                update_resources('gems',1,-10);
                update_resources('dungeon',1,-1);
                return 1;
            }
            return 0;

        default: //dicard card_id
            return 4;

    } //end switch
} // end play_card


//********************* MAIN*************************//

// Get the player game stat
$my_game_stat = get_my_game_stat();
$played_card = get_played_card_num();

// Check if player's turn
if ($my_game_stat['current_flag'] == false) {
    die("Error: Not Your Turn!");
}

$my_cards = array($my_game_stat['card1_id'],  $my_game_stat['card2_id'], $my_game_stat['card3_id'], $my_game_stat['card4_id'], $my_game_stat['card5_id'], $my_game_stat['card6_id']);
if (in_array($played_card, $my_cards)) {

    // Play the card
    $play_card_result = play_card($played_card);

    $result = $mysqli->query("UPDATE games SET last_played_card ='" .$played_card. "'
                WHERE game_id='" .$my_game_stat['game_id']. "'");
    if (!$result){
        echo "Update resources_cost SQL failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    // Check if anyone wins after played card
    check_for_win();

    get_new_card();

    switch ($play_card_result) {
        case 0: // Card Discarded
            echo "Your Card Will be Discarded!";
            break;

        case 1: // Played card one turn only
            break;

        case 2: // Played card got another turn
            $my_row = get_my_game_stat();
            update_resources('gems', 0, $my_row['magic']);
            update_resources('bricks', 0, $my_row['quarry']);
            update_resources('recruits', 0, $my_row['dungeon']);
            exit("You have another Turn");
            break;
    }

    // Update enemy resources for begining of his turn
    $enemy_row = get_enemy_game_stat();
    update_resources('gems', 1, $enemy_row['magic']);
    update_resources('bricks', 1, $enemy_row['quarry']);
    update_resources('recruits', 1, $enemy_row['dungeon']);

    // Check if anyone wins after update resources
    check_for_win();

    // Switch Turns
    update_resources('current_flag', 0, -1);
    update_resources('current_flag', 1, 1);
}// end if
else {
    echo "Player Don't Hold Card " . $card_id;
    //TODO: return: Suspicious activity detected! Player doesn't hold the card played.
}

$mysqli->close();
?>
