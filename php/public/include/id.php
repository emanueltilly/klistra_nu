<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/include/redis.php";

class IdGenerator
{
    private $word_list = [
        // Animals
        "bat",
        "bee",
        "cat",
        "cow",
        "dog",
        "eel",
        "elk",
        "fox",
        "frog",
        "gazelle",
        "goat",
        "hare",
        "hawk",
        "ibex",
        "jay",
        "kangaroo",
        "lemur",
        "lion",
        "llama",
        "lynx",
        "mole",
        "moose",
        "mouse",
        "newt",
        "orca",
        "otter",
        "owl",
        "panda",
        "pig",
        "pony",
        "quail",
        "rabbit",
        "rat",
        "seal",
        "shrew",
        "skunk",
        "sloth",
        "snake",
        "sparrow",
        "squirrel",
        "stoat",
        "swan",
        "tarsier",
        "tiger",
        "toad",
        "vole",
        "weasel",
        "whale",
        "wolf",
        "wombat",
        "yak",
        "zebra",
        "ape",
        "bat",
        "bug",
        "calf",
        "crab",
        "crow",
        "duck",
        "dove",
        "fawn",
        "ferret",
        "gull",
        "hawk",
        "koala",
        "lark",
        "lamb",
        "lark",
        "liger",
        "lynx",
        "mink",
        "moose",
        "mule",
        "newt",
        "otter",
        "oxen",
        "puma",
        "quail",
        "shark",
        "sheep",
        "skunk",
        "snail",
        "swan",
        "stoat",
        "tiger",
        "toad",
        "viper",
        "weasel",
        "whale",
        "yak",
        "zebra",

        //Fruits
        "apple",
        "apricot",
        "banana",
        "berry",
        "cherry",
        "date",
        "fig",
        "grape",
        "kiwi",
        "lemon",
        "lime",
        "mango",
        "melon",
        "olive",
        "orange",
        "peach",
        "pear",
        "plum",
        "prune",
        "quince",
        "raspberry",
        "tangerine",
        "watermelon",
        "avocado",
        "coconut",
        "currant",
        "elderberry",
        "grapefruit",
        "guava",
        "kiwifruit",
        "mulberry",
        "nectarine",
        "papaya",
        "persimmon",
        "pineapple",
        "pomegranate",
        "raisin",
        "strawberry",
        "blueberry",
        "cantaloupe",
        "clementine",
        "cranberry",
        "grapefruit",
        "honeydew",
        "kumquat",
        "mandarin",
        "marionberry",
        "passionfruit",
        "pepino",
        "pineapple",
        "pluot",
        "raspberry",
        "redcurrant",
        "tangelo",
        "tayberry",
        "boysenberry",
        "cherimoya",
        "cucumber",
        "damson",
        "feijoa",
        "guanabana",
        "jabuticaba",
        "loganberry",
        "lychee",
        "mangosteen",
        "pawpaw",
        "persimmon",
        "rambutan",
        "soursop",
        "yuzu",
        "acerola",
        "blackberry",
        "carambola",
        "goldenberry",
        "longan",
        "pitaya",
        "ugni",
        "yumberry",
    ];

    public function GetNew()
    {
        $redisConn = new RedisConn();

        $num_words = 1;
        $num_digits = 2;

        $unique = false;
        $tryCount = 0;

        while ($tryCount < 100 && $unique == false) {
            $tryCount++;
            $random_string = $this->generate_random_string(
                $this->word_list,
                $num_words,
                $num_digits
            );
            $redisResponse = $redisConn->Get($random_string);

            if ($redisResponse == false && strlen($redisResponse) < 1) {
                $unique = true;
                return $random_string;
                break;
            }
        }
        return $this->fallbackStringGenerator(8);
    }

    private function generate_random_string($word_list, $num_words, $num_digits)
    {
        // Initialize an empty array to hold the selected words
        $selected_words = [];

        // Select the specified number of random words from the list
        for ($i = 0; $i < $num_words; $i++) {
            $random_word = $word_list[array_rand($word_list)];
            $selected_words[] = $random_word;
        }

        // Combine the selected words with random digits
        $random_digits = mt_rand(
            pow(10, $num_digits - 1),
            pow(10, $num_digits) - 1
        );
        $random_string = implode("", $selected_words) . $random_digits;

        return $random_string;
    }

    private function fallbackStringGenerator($length)
    {
        $characters =
            "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
