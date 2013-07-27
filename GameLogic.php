<?php




    $con=mysqli_connect("localhost", "root", "12345", "test");
    // Check connection
    if (mysqli_connect_errno())
      {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
      }

    tower($type,$amount) //if reaches 0 - lose, if reaches max - win
    {
        switch($type)
        {
        case 0: //update current player

            break;

        case 1: //update enemy

            break;

        case 2: //update all

            break;
        }
    }


    wall($type,$amount) //if reaches 0, demage to tower
    {
        switch($type)
        {
        case 0: //update current player

            break;

        case 1: //update enemy

            break;

        case 2: //update all

            break;
        }
    }


    magic($type,$amount)
    {
        switch($type)
        {
        case 0: //update current player

            break;

        case 1: //update enemy

            break;

        case 2: //update all

            break;
        }
    }

    gems($type,$amount)
    {
        switch($type)
        {
        case 0: //update current player

            break;

        case 1: //update enemy

            break;

        case 2: //update all

            break;
        }
    }

    quarry($type,$amount)
    {
        switch($type)
        {
        case 0: //update current player

            break;

        case 1: //update enemy

            break;

        case 2: //update all

            break;
        }
    }

    bricks($type,$amount)
    {
        switch($type)
        {
        case 0: //update current player

            break;

        case 1: //update enemy

            break;

        case 2: //update all

            break;
        }
    }

    dungeon($type,$amount)
    {
        switch($type)
        {
        case 0: //update current player

            break;

        case 1: //update enemy

            break;

        case 2: //update all

            break;
        }
    }

    recruits($type,$amount)
    {
        switch($type)
        {
        case 0: //update current player

            break;

        case 1: //update enemy

            break;

        case 2: //update all

            break;
        }
    }


    cost($resource,$amount)
    {
        switch($resource)
        {
        case 0: //bricks
            //if not enough - print "not enough bricks, return 0
            return 1;

        case 1: //gems
            //if not enough - print "not enough gems, return 0
            return 1;

        case 2: //recruits
            //if not enough - print "not enough recruits, return 0
            return 1;
        }
    }

    get_wall($player)
    {
        if($player)
        {
            // return enemy's wall
        }
        //return my wall
    }

    get_tower($player)
    {
        if($player)
        {
            // return enemy's tower
        }
        //return my tower
    }

    get_magic($player)
    {
        if($player)
        {
            // return enemy's magic
        }
        //return my magic
    }

    get_quarry($player)
    {
        if($player)
        {
            // return enemy's quarry
        }
        //return my quarry
    }

    get_dungeon($player)
    {
        if($player)
        {
            // return enemy's dungeon
        }
        //return my dungeon
    }

    draw()
    {

    }

    discard()
    {

    }

    play_card($card_id)
    {

        switch ($card_id)
        {

             //**********************************//
		    //---------- Pink cards: ----------//

        case 1: //Brick Shortage
            bricks(2,-8);
            return 1;

        case 2: //Lucky Cache
            bricks(0,2);
            gems(0,2);
            return 2;

        case 3: //Earthquake

            break;

        case 4: //Strip Mine
            quarry(2,-1);
            return 1;

        case 5: //Friendly Terrain
            if(cost(0,1))
            {
                wall(0,1);
                return 2;
            }
            return 0;

        case 6: //Rock Garden
            if(cost(0,1))
            {
                wall(0,1);
                tower(0,1);
                recruits(0,2);
                return 1;
            }
            return 0;

        case 7: //Work overtime
            if(cost(0,2))
            {
                wall(0,5);
                gems(0,-6);
                return 1;
            }
            return 0;

        case 8: //Basic Wall
            if(cost(0,2))
            {
                wall(0,3);
                return 1;
            }
            return 0;


        case 9: //Innovations
            if(cost(0,2))
            {
                quarry(2,1);
                gems(0,4);
                return 1;
            }
            return 0;

        case 10: //Miners
            if(cost(0,3))
            {
                quarry(0,1);
                return 1;
            }
            return 0;

        case 11: //Sturdy Wall
            if(cost(0,3))
            {
                wall(0,4);
                return 1;
            }
            return 0;

        case 12: //Foundations
            if(cost(0,3))
            {
                if(get_wall(0)==0)
                {
                    wall(0,6);
                }
                else
                {
                    wall(0,3);
                }
                return 1;
            }
            return 0;

        case 13: //Mother Lode
            if(cost(0,4))
            {
                if(get_quarry(0)<get_quarry(1))
                {
                    quarry(0,2);
                }
                else
                {
                    quarry(0,1);
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
                quarry(1,-1);
                return 1;
            }
            return 0;

        case 16: //Big Wall
            if(cost(0,5))
            {
                wall(0,6);
                return 1;
            }
            return 0;

        case 17: //New Equipment
            if(cost(0,5))
            {
                quarry(0,2);
                return 1;
            }
            return 0;

        case 18: //Flood Water
            if(cost(0,6))
            {
                if(get_wall(0)<=get_wall(1))
                {
                    dungeon(0,-1);
                    tower(0,-2);
                }
                if(get_wall(1)<=get_wall(2))
                {
                    dungeon(1,-1);
                    tower(1,-2);
                }
                return 1;
            }
            return 0;

        case 19: //Dwarven Miners
            if(cost(0,7))
            {
                wall(0,14);
                quarry(0,1);
                return 1;
            }
            return 0;

        case 20: //Tremors
            if(cost(0,7))
            {
                wall(2,-5);
                return 2;
            }
            return 0;

        case 21: //Forced Labor
            if(cost(0,7))
            {
                wall(0,9);
                recruits(0,-5);
                return 1;
            }
            return 0;

        case 22: //Secret Room
            if(cost(0,8))
            {
                magic(0,1);
                return 2;
            }
            return 0;

        case 23: //Reinforced Wall
            if(cost(0,8))
            {
                wall(0,8);
                return 1;
            }
            return 0;

        case 24: //Porticulus
            if(cost(0,9))
            {
                wall(0,5);
                dungeon(0,1);
                return 1;
            }
            return 0;

        case 25: //Crystal Rocks
            if(cost(0,9))
            {
                wall(0,7);
                gems(0,7);
                return 1;
            }
            return 0;

        case 26: //Barracks
            if(cost(0,10))
            {
                recruits(0,6);
                wall(0,6);
                if(get_dungeon(0)<get_dungeon(1))
                {
                    dungeon(0,1);
                }
                return 1;
            }
            return 0;

        case 27: //Harmonic Ore
            if(cost(0,11))
            {
                wall(0,6);
                tower(0,3);
                return 1;
            }
            return 0;

        case 28: //MondoWall
            if(cost(0,13))
            {
                wall(0,12);
                return 1;
            }
            return 0;

        case 29: //Battlemnets
            if(cost(0,14))
            {
                wall(0,7);
                wall(1,-6);
                return 1;
            }
            return 0;

        case 30: //Focused Designs
            if(cost(0,15))
            {
                wall(0,8);
                tower(0,5);
                return 1;
            }
            return 0;

        case 31: //Great Wall
            if(cost(0,16))
            {
                wall(0,15);
                return 1;
            }
            return 0;

        case 32: //Shift
            if(cost(0,17))
            {
                $tmp = get_wall(0) - get_wall(1);
                wall(0,-$tmp);
                wall(1,$tmp);
                return 1;
            }
            return 0;

        case 33: //Rock Launcher
            if(cost(0,18))
            {
                wall(0,6);
                wall(1,-10);
                return 1;
            }
            return 0;

        case 34: //Dragon's Heart
            if(cost(0,24))
            {
                wall(20);
                tower(8);
                return 1;
            }
            return 0;

             //**********************************//
		    //---------- Blue cards: ----------//


        case 35: //Bag of Baubles
            if(get_tower(0)<get_tower(1))
            {
                tower(0,2);
            }
            else
            {
                tower(0,1);
            }
            return 1;

        case 36: //Rainbow
            tower(2,1);
            gems(0,3);
            return 1;

        case 37: //Quartz
            if(cost(1,1))
            {
                tower(0,1);
                return 2;
            }
            return 0;

        case 38: //Smoky Quartz
            if(cost(1,2))
            {
                tower(1,-1);
                return 2;
            }
            return 0;

        case 39: //Amethyst
            if(cost(1,2))
            {
                tower(0,3);
                return 1;
            }
            return 0;

        case 40: //Prism
            if(cost(1,2))
            {
                draw();
                discard();
                return 2;
            }
            return 0;

        case 41: //Gemstone Flaw
            if(cost(1,2))
            {
                tower(1,-3);
                return 1;
            }
            return 0;

        case 42: //Spell Weavers
            if(cost(1,3))
            {
                magic(0,1);
                return 1;
            }
            return 0;

        case 43: //Ruby
            if(cost(1,3))
            {
                tower(0,5);
                return 1;
            }
            return 0;

        case 44: //Power Burn
            if(cost(1,3))
            {
                tower(0,-5);
                magic(0,2);
                return 1;
            }
            return 0;

        case 45: //Solar Flare
            if(cost(1,4))
            {
                tower(0,2);
                tower(1,-2);
                return 1;
            }
            return 0;

        case 46: //Gem Spear
            if(cost(1,4))
            {
                tower(1,-5);
                return 1;
            }
            return 0;

        case 47: //Quarry's Help
            if(cost(1,4))
            {
                tower(0,7);
                bricks(0,-10);
                return 1;
            }
            return 0;

        case 48: //Lodestone
            if(cost(1,5))
            {
                tower(0,3); //TODO !!!!!!!!!!!!!!!! can't be discarded!!!!!!!!
                return 1;
            }
            return 0;

        case 49: //Discord
            if(cost(1,5))
            {
                tower(2,-7);
                magic(2,-1);
                return 1;
            }
            return 0;

        case 50: //Apprentice
            if(cost(1,5))
            {
                tower(0,4);
                recruits(0,-3);
                tower(1,-2);
                return 1;
            }
            return 0;

        case 51: //Crystal Matrix
            if(cost(1,6))
            {
                magic(0,2);
                tower(0,3)
                tower(1,1);
                return 1;
            }
            return 0;

        case 52: //Emerald
            if(cost(1,6))
            {
                tower(0,8);
                return 1;
            }
            return 0;

        case 53: //Harmonic Vibe
            if(cost(1,7))
            {
                magic(0,1);
                tower(0,3);
                wall(0,3);
                return 1;
            }
            return 0;

        case 54: //Parity
            if(cost(1,7))
            {
                if(get_magic(0)<get_magic(1))
                {
                    magic(0, get_magic(1)-get_magic(0) );
                }
                else
                {
                    magic(1, get_magic(0)-get_magic(1) );
                }
                return 1;
            }
            return 0;

        case 55: //Crumblestone
            if(cost(1,7))
            {
                tower(0,5);
                bricks(1,-6);
                return 1;
            }
            return 0;

        case 56: //Shatterer
            if(cost(1,8))
            {
                magic(0,-1);
                tower(1,-9);
                return 1;
            }
            return 0;

        case 57: //Crystallize
            if(cost(1,8))
            {
                tower(0,11);
                wall(0,-6);
                return 1;
            }
            return 0;

        case 58: //Pearl Of Wisdom
            if(cost(1,9))
            {
                tower(0,5);
                magic(0,1);
                return 1;
            }
            return 0;

        case 59: //Sapphire
            if(cost(1,10))
            {
                tower(0,11);
                return 1;
            }
            return 0;

        case 60: //Lightning Shard
            if(cost(1,11))
            {
                if(get_tower(0)>get_wall(1))
                {
                    tower(1,-8);
                }
                else
                {
                    wall(1,-8);
                }
                return 1;
            }
            return 0;

        case 61: //Crystal Shield
            if(cost(1,12))
            {
                tower(0,8);
                wall(0,3);
                return 1;
            }
            return 0;

        case 62: //Fire Ruby
            if(cost(1,12))
            {
                tower(0,6);
                tower(1,-4);
                return 1;
            }
            return 0;

        case 63: //Empathy Gem
            if(cost(1,14))
            {
                tower(0,8);
                dungeon(0,1);
                return 1;
            }
            return 0;

        case 64: //Sanctuary
            if(cost(1,15))
            {
                tower(0,10);
                wall(0,5);
                recruits(0,5);
                return 1;
            }
            return 0;

        case 65: //Diamond
            if(cost(1,16))
            {
                tower(0,15);
                return 1;
            }
            return 0;

        case 66: //Lava Jewel
            if(cost(1,17))
            {
                tower(0,12);
                wall(1,-6);
                return 1;
            }
            return 0;

        case 67: //Phase Jewel
            if(cost(1,18))
            {
                tower(0,13);
                recruits(0,6);
                bricks(0,6);
                return 1;
            }
            return 0;

        case 68: //Dragon's Eye
            if(cost(1,21))
            {
                tower(0,20);
                return 1;
            }
            return 0;

             //***********************************//
		    //---------- Green cards: ----------//

        case 69: // MadCowDisease
            recruits(2,-6);
            return 1;

        case 70: //Full Moon
            dungeon(2,1);
            recruits(0,3);
            return 1;

        case 71: //Faerie
            if(cost(2,1))
            {
                wall(1,-2);
                return 2;
            }
            return 0;

        case 72: //Moody Goblins
            if(cost(2,1))
            {
                wall(1,-4)
                gems(0,-3);
                return 1;
            }
            return 0;

        case 73: //Elven Scout
            if(cost(2,2))
            {
                draw();
                discard();
                return 2;
            }
            return 0;

        case 74: //Spearman
            if(cost(2,2))
            {
                if(get_wall(0)>get_wall(1))
                {
                    wall(1,-3);
                }
                else
                {
                    wall(1,-2);
                }
                return 1;
            }
            return 0;

        case 75: //Gnome
            if(cost(2,2))
            {
                wall(1,-3);
                gems(0,1);
                return 1;
            }
            return 0;

        case 76: //Minotaur
            if(cost(2,3))
            {
                dungeon(0,1);
                return 1;
            }
            return 0;

        case 77: //Goblin Mob
            if(cost(2,3))
            {
                wall(1,-6);
                wall(0,-3);
                return 1;
            }
            return 0;

        case 78: //Orc
            if(cost(2,3))
            {
                wall(1,-5);
                return 1;
            }
            return 0;

        case 79: //Goblin Archers
            if(cost(2,4))
            {
                tower(1,-3);
                wall(0,-1);
                return 1;
            }
            return 0;

        case 80: //Berserker
            if(cost(2,4))
            {
                wall(1,-8);
                tower(0,-3);
                return 1;
            }
            return 0;

        case 81: //Dwarves
            if(cost(2,5))
            {
                wall(1,-4);
                wall(0,3);
                return 1;
            }
            return 0;

        case 82: //Slasher
            if(cost(2,5))
            {
                wall(1,-6);
                return 1;
            }
            return 0;

        case 83: //Imp
            if(cost(2,5))
            {
                wall(1,-6);
                bricks(2,-5);
                gems(2,-5);
                recruits(2,-5);
                return 1;
            }
            return 0;

        case 84: //Shadow Faerie
            if(cost(2,6))
            {
                tower(1,-2);
                return 2;
            }
            return 0;

        case 85: //Little Snakes
            if(cost(2,6))
            {
                tower(1,-4);
                return 1;
            }
            return 0;

        case 86: //Ogre
            if(cost(2,6))
            {
                wall(1,-7);
                return 1;
            }
            return 0;

        case 87: //Rabid Sheep
            if(cost(2,6))
            {
                wall(1,-6);
                recruits(1,-3);
                return 1;
            }
            return 0;

        case 88: //Troll Trainer
            if(cost(2,7))
            {
                dungeon(0,2);
                return 1;
            }
            return 0;

        case 89: //Tower Gremlin
            if(cost(2,8))
            {
                wall(1,-2);
                wall(0,4);
                tower(0,2);
                return 1;
            }
            return 0;

        case 90: //Spizzer
            if(cost(2,8))
            {
                of(get_wall(1)==0)
                {
                    wall(1,-10);
                }
                else
                {
                    wall(1,-6);
                }
                return 1;
            }
            return 0;

        case 91: //Werewolf
            if(cost(2,9))
            {
                wall(1,-9);
                return 1;
            }
            return 0;

        case 92: //Unicorn
            if(cost(2,9))
            {
                if(get_magic(0)>get_magic(1))
                {
                    wall(1,-12);
                }
                else
                {
                    wall(1,-8);
                }
                return 1;
            }
            return 0;

        case 93: //Elven Archers
            if(cost(2,10))
            {
                if(get_wall(0)>get_wall(1)) {
                    tower(1,-6);
                }
                else {
                    wall(1,-6);
                }
                return 1;
            }
            return 0;

        case 94: //Corrosion Clouds
            if(cost(2,11))
            {
                if(get_wall(1)>0) {
                    wall(1,-10);
                }
                else {
                    wall(1,-7);
                }
                return 1;
            }
            return 0;

        case 95: //Rock Stompers
            if(cost(2,11))
            {
                wall(1,-8);
                quarry(1,-1);
                return 1;
            }
            return 0;

        case 96: //Thief
            if(cost(2,12))
            {
                gems(1,-10);
                bricks(1,-5);
                gems(0,5);
                bricks(0,3); // TODO: currect ???????????????
                return 1;
            }
            return 0;

        case 97: //Warlord
            if(cost(2,13))
            {
                wall(1,-13);
                gems(0,-3);
                return 1;
            }
            return 0;

        case 98: //Succubus
            if(cost(2,14))
            {
                tower(1,-5);
                recruits(1,-8);
                return 1;
            }
            return 0;

        case 99: //Stone Giant
            if(cost(2,15))
            {
                wall(1,-10);
                wall(0,4);
                return 1;
            }
            return 0;

        case 100: //Vampire
            if(cost(2,17))
            {
                wall(1,-10);
                recruits(1,-5);
                dungeon(1,-1);
                return 1;
            }
            return 0;

        case 101: //Pegasus Lancer
            if(cost(2,18))
            {
                tower(1,-12);
                return 1;
            }
            return 0;

        case 102: //Dragon
            if(cost(2,25))
            {
                wall(1,-20);
                gems(1,-10);
                dungeon(1,-1);
                return 1;
            }
            return 0;

        default:

        }
    }



    mysqli_query($con,"UPDATE game SET Age=36
    WHERE FirstName='Peter' AND LastName='Griffin'");

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
