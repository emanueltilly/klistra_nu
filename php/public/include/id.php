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
        "goat",
        "hare",
        "hawk",
        "jay",
        "lemur",
        "lion",
        "lynx",
        "mole",
        "moose",
        "mouse",
        "orca",
        "otter",
        "owl",
        "panda",
        "pig",
        "pony",
        "rabbit",
        "rat",
        "seal",
        "sloth",
        "snake",
        "swan",
        "tiger",
        "toad",
        "whale",
        "wolf",
        "zebra",
        "ape",
        "bat",
        "bug",
        "crab",
        "crow",
        "duck",
        "dove",
        "hawk",
        "koala",
        "lamb",
        "lynx",
        "mink",
        "moose",
        "otter",
        "puma",
        "shark",
        "sheep",
        "snail",
        "swan",
        "tiger",
        "toad",
        "viper",
        "whale",

        //Fruits
        "apple",
        "banana",
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
        "avocado",
        "coconut",
        "currant",
        "guava",
        "papaya",
        "persimmon",
        "pineapple",
        "raisin",
        "strawberry",
        "blueberry",
        "cranberry",
        "grapefruit",
        "raspberry",
        "tangelo",
        "tayberry",
        "cucumber"
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
