<?php

    $con=mysqli_connect("localhost", "root", "12345", "test");
    // Check connection
    if (mysqli_connect_errno())
      {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
      }

    get_my_row()
    {
        session_start();
        $my_result = $mysqli->query("SELECT * FROM games WHERE game_id='".$_SESSION['game_id']."' AND nickname = '".$_SESSION['nickname']."'");
        return mysqli_fetch_array($my_result);
    }

    get_enemy_row()
    {
        session_start();
        $enemy_result = $mysqli->query("SELECT * FROM games WHERE game_id = '".$_SESSION['game_id']."' AND nickname != '".$_SESSION['nickname']."'");
        return mysqli_fetch_array($enemy_result);
    }

    check_for_win($my_row,$enemy_row)
    {
        if( ($my_row['gems'] >= 200  && $my_row['bricks'] >= 200 && $my_row['recruits'] >= 200
                || $my_row['tower'] >= 100
                || $enemy_row['tower'] <= 0 )
            && ($enemy_row['gems'] >= 200  && $enemy_row['bricks'] >= 200 && $enemy_row['recruits'] >= 200
                || $enemy_row['tower'] >= 100
                || $my_row['tower'] <= 0 ) ) {
                    //TODO: return to both users "It's a tie!"
                    retuen 2; //there's a tie
        }
        if($my_row['gems'] >= 200  && $my_row['bricks'] >= 200 && $my_row['recruits'] >= 200
            || $my_row['tower'] >= 100
            || $enemy_row['tower'] <= 0 ){
            //TODO: return to current user: You win!, to opponent: You loose!
            return 1; //I win
        }
        if(($enemy_row['gems'] >= 200  && $enemy_row['bricks'] >= 200 && $enemy_row['recruits'] >= 200
            || $enemy_row['tower'] >= 100
            || $my_row['tower'] <= 0 )) {
            //TODO: return to current user: You loose!, to opponent: You win!
            return 2; //enemy wins
        }
        return 0; //no win
    } //returns 0 if no win, 1 if I win, 2 if enemy wins, 3 if it's a tie


    update($recource,$player,$amount)
    {
        $my_row = get_my_row();
        $enemy_row = get_enemy_row();
        switch($player) {

        case 0: //update current player
            $mysqli->query("UPDATE games SET '$recource'='".max(0,$my_row[$recource] + $amount)."'
                    WHERE game_id='".$my_row['game_id']."' AND nickname='".$my_row['nickname']."'");
            if($recource=='wall' && $my_row['wall'] + $amount < 0) {
                update('tower',0,$my_row['wall'] + $amount);
            }
            return;

        case 1: //update enemy
            $mysqli->query("UPDATE games SET '$recource'='".max(0,$enemy_row[$recource] + $amount)."'
                    WHERE game_id='".$enemy_row['game_id']."' AND nickname='".$enemy_row['nickname']."'");
             if($recource=='wall' && $enemy_row['wall'] + $amount < 0) {
                update('tower',0,$enemy_row['wall'] + $amount);
            }
            return;

        case 2: //update all
            update($recource,0,$amount);
            update($recource,1,$amount);
            return;
        }
    }

    cost($resource,$amount)
    {
        $my_row = get_my_row();

        if($my_row[$resource] - $amount < 0) {
            return 0;
        }
        $mysqli->query("UPDATE games SET '$recource'='".($my_row[$recource] - $amount)."'
                    WHERE game_id='".$my_row['game_id']."' AND nickname='".$my_row['nickname']."'");
    }

    get_resource($recource,$player)
    {
        if($player) {
            $enemy_row = get_enemy_row();
            return $enemy_row[$recource];
        }
        $my_row = get_my_row();
        return $my_row[$resource];
    }


    play_card($card_id)
    {

        switch ($card_id)
        {

             //**********************************//
		    //---------- Pink cards: ----------//

        case 1: //Brick Shortage
            update('bricks',2,-8);
            return 1;

        case 2: //Lucky Cache
            update('bricks',0,2);
            update('gems',0,2);
            return 2;

        case 3: //Earthquake

            break;

        case 4: //Strip Mine
            update('quarry',2,-1);
            return 1;

        case 5: //Friendly Terrain
            if(cost(0,1))
            {
                update('wall',0,1);
                return 2;
            }
            return 0;

        case 6: //Rock Garden
            if(cost(0,1))
            {
                update('wall',0,1);
                update('tower',0,1);
                update('recruits',0,2);
                return 1;
            }
            return 0;

        case 7: //Work overtime
            if(cost(0,2))
            {
                update('wall',0,5);
                update('gems',0,-6);
                return 1;
            }
            return 0;

        case 8: //Basic Wall
            if(cost(0,2))
            {
                update('wall',0,3);
                return 1;
            }
            return 0;


        case 9: //Innovations
            if(cost(0,2))
            {
                update('quarry',2,1);
                update('gems',0,4);
                return 1;
            }
            return 0;

        case 10: //Miners
            if(cost(0,3))
            {
                update('quarry',0,1);
                return 1;
            }
            return 0;

        case 11: //Sturdy Wall
            if(cost(0,3))
            {
                update('wall',0,4);
                return 1;
            }
            return 0;

        case 12: //Foundations
            if(cost(0,3))
            {
                if(get_update('wall',0)==0)
                {
                    update('wall',0,6);
                }
                else
                {
                    update('wall',0,3);
                }
                return 1;
            }
            return 0;

        case 13: //Mother Lode
            if(cost(0,4))
            {
                if(get_resource('quarry',0)<get_resource('quarry',1))
                {
                    update('quarry',0,2);
                }
                else
                {
                    update('quarry',0,1);
                }
                return 1;
            }
            return 0;

        case 14: //collapse!
            if(cost(0,4))
            {

                return 1;
            }
            return 0;

        case 15: //Copping the Tech
            if(cost(0,5))
            {
                update('quarry',1,-1);
                return 1;
            }
            return 0;

        case 16: //Big Wall
            if(cost(0,5))
            {
                update('wall',0,6);
                return 1;
            }
            return 0;

        case 17: //New Equipment
            if(cost(0,5))
            {
                update('quarry',0,2);
                return 1;
            }
            return 0;

        case 18: //Flood Water
            if(cost(0,6))
            {
                if(get_resource('wall',0)<=get_resource('wall',1))
                {
                    update('dungeon',0,-1);
                    update('tower',0,-2);
                }
                if(get_resource('wall',1)<=get_resource('wall',2))
                {
                    update('dungeon',1,-1);
                    update('tower',1,-2);
                }
                return 1;
            }
            return 0;

        case 19: //Dwarven Miners
            if(cost(0,7))
            {
                update('wall',0,14);
                update('quarry',0,1);
                return 1;
            }
            return 0;

        case 20: //Tremors
            if(cost(0,7))
            {
                update('wall',2,-5);
                return 2;
            }
            return 0;

        case 21: //Forced Labor
            if(cost(0,7))
            {
                update('wall',0,9);
                update('recruits',0,-5);
                return 1;
            }
            return 0;

        case 22: //Secret Room
            if(cost(0,8))
            {
                update('magic',0,1);
                return 2;
            }
            return 0;

        case 23: //Reinforced Wall
            if(cost(0,8))
            {
                update('wall',0,8);
                return 1;
            }
            return 0;

        case 24: //Porticulus
            if(cost(0,9))
            {
                update('wall',0,5);
                update('dungeon',0,1);
                return 1;
            }
            return 0;

        case 25: //Crystal Rocks
            if(cost(0,9))
            {
                update('wall',0,7);
                update('gems',0,7);
                return 1;
            }
            return 0;

        case 26: //Barracks
            if(cost(0,10))
            {
                update('recruits',0,6);
                update('wall',0,6);
                if(get_resource('dungeon',0)<get_resource('dungeon',1))
                {
                    update('dungeon',0,1);
                }
                return 1;
            }
            return 0;

        case 27: //Harmonic Ore
            if(cost(0,11))
            {
                update('wall',0,6);
                update('tower',0,3);
                return 1;
            }
            return 0;

        case 28: //MondoWall
            if(cost(0,13))
            {
                update('wall',0,12);
                return 1;
            }
            return 0;

        case 29: //Battlemnets
            if(cost(0,14))
            {
                update('wall',0,7);
                update('wall',1,-6);
                return 1;
            }
            return 0;

        case 30: //Focused Designs
            if(cost(0,15))
            {
                update('wall',0,8);
                update('tower',0,5);
                return 1;
            }
            return 0;

        case 31: //Great Wall
            if(cost(0,16))
            {
                update('wall',0,15);
                return 1;
            }
            return 0;

        case 32: //Shift
            if(cost(0,17))
            {
                $tmp = get_resource('wall',0) - get_resource('wall',1);
                update('wall',0,-$tmp);
                update('wall',1,$tmp);
                return 1;
            }
            return 0;

        case 33: //Rock Launcher
            if(cost(0,18))
            {
                update('wall',0,6);
                update('wall',1,-10);
                return 1;
            }
            return 0;

        case 34: //Dragon's Heart
            if(cost(0,24))
            {
                update('wall',20);
                update('tower',8);
                return 1;
            }
            return 0;

             //**********************************//
		    //---------- Blue cards: ----------//


        case 35: //Bag of Baubles
            if(get_resource('tower',0)<get_resource('tower',1))
            {
                update('tower',0,2);
            }
            else
            {
                update('tower',0,1);
            }
            return 1;

        case 36: //Rainbow
            update('tower',2,1);
            update('gems',0,3);
            return 1;

        case 37: //Quartz
            if(cost(1,1))
            {
                update('tower',0,1);
                return 2;
            }
            return 0;

        case 38: //Smoky Quartz
            if(cost(1,2))
            {
                update('tower',1,-1);
                return 2;
            }
            return 0;

        case 39: //Amethyst
            if(cost(1,2))
            {
                update('tower',0,3);
                return 1;
            }
            return 0;

        case 40: //Prism
            if(cost(1,2))
            {
                return 3;
            }
            return 0;

        case 41: //Gemstone Flaw
            if(cost(1,2))
            {
                update('tower',1,-3);
                return 1;
            }
            return 0;

        case 42: //Spell Weavers
            if(cost(1,3))
            {
                update('magic',0,1);
                return 1;
            }
            return 0;

        case 43: //Ruby
            if(cost(1,3))
            {
                update('tower',0,5);
                return 1;
            }
            return 0;

        case 44: //Power Burn
            if(cost(1,3))
            {
                update('tower',0,-5);
                update('magic',0,2);
                return 1;
            }
            return 0;

        case 45: //Solar Flare
            if(cost(1,4))
            {
                update('tower',0,2);
                update('tower',1,-2);
                return 1;
            }
            return 0;

        case 46: //Gem Spear
            if(cost(1,4))
            {
                update('tower',1,-5);
                return 1;
            }
            return 0;

        case 47: //Quarry's Help
            if(cost(1,4))
            {
                update('tower',0,7);
                update('bricks',0,-10);
                return 1;
            }
            return 0;

        case 48: //Lodestone
            if(cost(1,5))
            {
                update('tower',0,3); //TODO !!!!!!!!!!!!!!!! can't be discarded!!!!!!!!
                return 1;
            }
            return 0;

        case 49: //Discord
            if(cost(1,5))
            {
                update('tower',2,-7);
                update('magic',2,-1);
                return 1;
            }
            return 0;

        case 50: //Apprentice
            if(cost(1,5))
            {
                update('tower',0,4);
                update('recruits',0,-3);
                update('tower',1,-2);
                return 1;
            }
            return 0;

        case 51: //Crystal Matrix
            if(cost(1,6))
            {
                update('magic',0,2);
                update('tower',0,3)
                update('tower',1,1);
                return 1;
            }
            return 0;

        case 52: //Emerald
            if(cost(1,6))
            {
                update('tower',0,8);
                return 1;
            }
            return 0;

        case 53: //Harmonic Vibe
            if(cost(1,7))
            {
                update('magic',0,1);
                update('tower',0,3);
                update('wall',0,3);
                return 1;
            }
            return 0;

        case 54: //Parity
            if(cost(1,7))
            {
                if(get_resource('magic',0)<get_resource('magic',1))
                {
                    update('magic',0, get_resource('magic',1)-get_resource('magic',0) );
                }
                else
                {
                    update('magic',1, get_resource('magic',0)-get_resource('magic',1) );
                }
                return 1;
            }
            return 0;

        case 55: //Crumblestone
            if(cost(1,7))
            {
                update('tower',0,5);
                update('bricks',1,-6);
                return 1;
            }
            return 0;

        case 56: //Shatterer
            if(cost(1,8))
            {
                update('magic',0,-1);
                update('tower',1,-9);
                return 1;
            }
            return 0;

        case 57: //Crystallize
            if(cost(1,8))
            {
                update('tower',0,11);
                update('wall',0,-6);
                return 1;
            }
            return 0;

        case 58: //Pearl Of Wisdom
            if(cost(1,9))
            {
                update('tower',0,5);
                update('magic',0,1);
                return 1;
            }
            return 0;

        case 59: //Sapphire
            if(cost(1,10))
            {
                update('tower',0,11);
                return 1;
            }
            return 0;

        case 60: //Lightning Shard
            if(cost(1,11))
            {
                if(get_resource('tower',0)>get_resource('wall',1))
                {
                    update('tower',1,-8);
                }
                else
                {
                    update('wall',1,-8);
                }
                return 1;
            }
            return 0;

        case 61: //Crystal Shield
            if(cost(1,12))
            {
                update('tower',0,8);
                update('wall',0,3);
                return 1;
            }
            return 0;

        case 62: //Fire Ruby
            if(cost(1,12))
            {
                update('tower',0,6);
                update('tower',1,-4);
                return 1;
            }
            return 0;

        case 63: //Empathy Gem
            if(cost(1,14))
            {
                update('tower',0,8);
                update('dungeon',0,1);
                return 1;
            }
            return 0;

        case 64: //Sanctuary
            if(cost(1,15))
            {
                update('tower',0,10);
                update('wall',0,5);
                update('recruits',0,5);
                return 1;
            }
            return 0;

        case 65: //Diamond
            if(cost(1,16))
            {
                update('tower',0,15);
                return 1;
            }
            return 0;

        case 66: //Lava Jewel
            if(cost(1,17))
            {
                update('tower',0,12);
                update('wall',1,-6);
                return 1;
            }
            return 0;

        case 67: //Phase Jewel
            if(cost(1,18))
            {
                update('tower',0,13);
                update('recruits',0,6);
                update('bricks',0,6);
                return 1;
            }
            return 0;

        case 68: //Dragon's Eye
            if(cost(1,21))
            {
                update('tower',0,20);
                return 1;
            }
            return 0;

             //***********************************//
		    //---------- Green cards: ----------//

        case 69: // MadCowDisease
            update('recruits',2,-6);
            return 1;

        case 70: //Full Moon
            update('dungeon',2,1);
            update('recruits',0,3);
            return 1;

        case 71: //Faerie
            if(cost(2,1))
            {
                update('wall',1,-2);
                return 2;
            }
            return 0;

        case 72: //Moody Goblins
            if(cost(2,1))
            {
                update('wall',1,-4)
                update('gems',0,-3);
                return 1;
            }
            return 0;

        case 73: //Elven Scout
            if(cost(2,2))
            {
                return 3;
            }
            return 0;

        case 74: //Spearman
            if(cost(2,2))
            {
                if(get_resource('wall',0)>get_resource('wall',1))
                {
                    update('wall',1,-3);
                }
                else
                {
                    update('wall',1,-2);
                }
                return 1;
            }
            return 0;

        case 75: //Gnome
            if(cost(2,2))
            {
                update('wall',1,-3);
                update('gems',0,1);
                return 1;
            }
            return 0;

        case 76: //Minotaur
            if(cost(2,3))
            {
                update('dungeon',0,1);
                return 1;
            }
            return 0;

        case 77: //Goblin Mob
            if(cost(2,3))
            {
                update('wall',1,-6);
                update('wall',0,-3);
                return 1;
            }
            return 0;

        case 78: //Orc
            if(cost(2,3))
            {
                update('wall',1,-5);
                return 1;
            }
            return 0;

        case 79: //Goblin Archers
            if(cost(2,4))
            {
                update('tower',1,-3);
                update('wall',0,-1);
                return 1;
            }
            return 0;

        case 80: //Berserker
            if(cost(2,4))
            {
                update('wall',1,-8);
                update('tower',0,-3);
                return 1;
            }
            return 0;

        case 81: //Dwarves
            if(cost(2,5))
            {
                update('wall',1,-4);
                update('wall',0,3);
                return 1;
            }
            return 0;

        case 82: //Slasher
            if(cost(2,5))
            {
                update('wall',1,-6);
                return 1;
            }
            return 0;

        case 83: //Imp
            if(cost(2,5))
            {
                update('wall',1,-6);
                update('bricks',2,-5);
                update('gems',2,-5);
                update('recruits',2,-5);
                return 1;
            }
            return 0;

        case 84: //Shadow Faerie
            if(cost(2,6))
            {
                update('tower',1,-2);
                return 2;
            }
            return 0;

        case 85: //Little Snakes
            if(cost(2,6))
            {
                update('tower',1,-4);
                return 1;
            }
            return 0;

        case 86: //Ogre
            if(cost(2,6))
            {
                update('wall',1,-7);
                return 1;
            }
            return 0;

        case 87: //Rabid Sheep
            if(cost(2,6))
            {
                update('wall',1,-6);
                update('recruits',1,-3);
                return 1;
            }
            return 0;

        case 88: //Troll Trainer
            if(cost(2,7))
            {
                update('dungeon',0,2);
                return 1;
            }
            return 0;

        case 89: //Tower Gremlin
            if(cost(2,8))
            {
                update('wall',1,-2);
                update('wall',0,4);
                update('tower',0,2);
                return 1;
            }
            return 0;

        case 90: //Spizzer
            if(cost(2,8))
            {
                of(get_resource('wall',1)==0)
                {
                    update('wall',1,-10);
                }
                else
                {
                    update('wall',1,-6);
                }
                return 1;
            }
            return 0;

        case 91: //Werewolf
            if(cost(2,9))
            {
                update('wall',1,-9);
                return 1;
            }
            return 0;

        case 92: //Unicorn
            if(cost(2,9))
            {
                if(get_resource('magic',0)>get_resource('magic',1))
                {
                    update('wall',1,-12);
                }
                else
                {
                    update('wall',1,-8);
                }
                return 1;
            }
            return 0;

        case 93: //Elven Archers
            if(cost(2,10))
            {
                if(get_resource('wall',0)>get_resource('wall',1)) {
                    update('tower',1,-6);
                }
                else {
                    update('wall',1,-6);
                }
                return 1;
            }
            return 0;

        case 94: //Corrosion Clouds
            if(cost(2,11))
            {
                if(get_resource('wall',1)>0) {
                    update('wall',1,-10);
                }
                else {
                    update('wall',1,-7);
                }
                return 1;
            }
            return 0;

        case 95: //Rock Stompers
            if(cost(2,11))
            {
                update('wall',1,-8);
                update('quarry',1,-1);
                return 1;
            }
            return 0;

        case 96: //Thief
            if(cost(2,12))
            {
                update('gems',1,-10);
                update('bricks',1,-5);
                update('gems',0,5);
                update('bricks',0,3); // TODO: currect ???????????????
                return 1;
            }
            return 0;

        case 97: //Warlord
            if(cost(2,13))
            {
                update('wall',1,-13);
                update('gems',0,-3);
                return 1;
            }
            return 0;

        case 98: //Succubus
            if(cost(2,14))
            {
                update('tower',1,-5);
                update('recruits',1,-8);
                return 1;
            }
            return 0;

        case 99: //Stone Giant
            if(cost(2,15))
            {
                update('wall',1,-10);
                update('wall',0,4);
                return 1;
            }
            return 0;

        case 100: //Vampire
            if(cost(2,17))
            {
                update('wall',1,-10);
                update('recruits',1,-5);
                update('dungeon',1,-1);
                return 1;
            }
            return 0;

        case 101: //Pegasus Lancer
            if(cost(2,18))
            {
                update('tower',1,-12);
                return 1;
            }
            return 0;

        case 102: //Dragon
            if(cost(2,25))
            {
                update('wall',1,-20);
                update('gems',1,-10);
                update('dungeon',1,-1);
                return 1;
            }
            return 0;

        default: //dicard card_id
            return 4;

        } //end switch
    } // end play_card
    
    preg_match('/^[a-zA-Z0-9]+$/',$_REQUEST["card_id"]) ? $card_id = $_REQUEST["card_id"] : exit('XSS is detected!');

    $play_card_res = play(card_id);

    $my_row = get_my_row();
    $enemy_row = get_enemy_row();
    if(check_for_win($my_row,$enemy_row) == 0
        && $enemy_row['cards_played'] > 0
        && ($play_card_res == 1 || $play_card_res == 4)) {
            update('gems',0,$my_row['magic']);
            update('bricks',0,$my_row['quarry']);
            update('Recruits',0,$my_row['Dungeon']);
    }

    $my_row = get_my_row();
    $enemy_row = get_enemy_row();
    if(check_for_win($my_row,$enemy_row) == 0) {
        switch($play_card_res) {
            case 0: //could not play card, not enough resources
                //TODO: return card_id
                break;
            case 1: //played card, end turn
                update('current_flag',0,-1);
                update('current_flag',1,1);
                break;

            case 2: //played card, play again
                //TODO: return new card_id with rand(1,102)
                break;

            case 3: //played card, discard a card and play again
                update('discard_turn',0,1);
                //TODO: return new card_id with rand(1,102)
                break;
        
            case 4: //card_id discarded,
                if($my_row['discard_turn']) {
                    update('discard_turn',0,-1);
                }
                else {
                    update('current_flag',0,-1);
                    update('current_flag',1,1);
                }
                //TODO: return new card_id with rand(1,102)
                break;
        }//end switch
    }//end if


    mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title></title>
</head>
<body></body>
</html>
