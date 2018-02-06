<?php
echo $argv[1];
file_put_contents('1.txt', $argv[1], FILE_APPEND);