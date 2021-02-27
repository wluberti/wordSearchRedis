<?php

require __DIR__ . '/vendor/autoload.php';

/*
    Populate the file defined in ./webroot/FILENAME with your dictionairy.
    An example for the entire Dutch language:
        https://github.com/OpenTaal/opentaal-wordlist/raw/master/wordlist.txt

    @author Wouter Luberti <spammemaar+github@xs4all.nl>
*/

$FILENAME = 'wordlist.txt';

$redis = new Predis\Client('tcp://redis:6379');

if ($redis->info()['Keyspace'] === []) {
    echo 'No database found. Populating database...' . PHP_EOL;

    if (!file_exists($FILENAME)) {
        die(sprintf(
            '"%s" does not exist! See the comment in %s for details' . PHP_EOL,
            $FILENAME,
            __FILE__
        ));
    }

    $wordlist = explode("\n", file_get_contents($FILENAME));

    foreach ($wordlist as $word) {
        // Skip 'illigal' words
        if (   str_contains($word, ' ')     // No spaces
            || str_contains($word, '-')     // No combined words
            || str_contains($word, "'")     // No words with appostrophes
            || preg_match('/[A-Z]/', $word) // No words containing capital letters
            || preg_match('/\d/', $word)    // No words containing digits
        ){
            continue;
        }
        $redis->set($word, $word);
    }
    echo 'done...' . PHP_EOL;
}

echo 'this: ' . $redis->get('busstation');