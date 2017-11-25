# warehouse

To run tests:
1. Run composer install
2. cd into the root directory (on the same level as phpunit.xml) and run phpunit

To run the cli:
Run: src/cli/PreparePickRun.php /full_path_to_input.csv /full_path_to_output.csv

Design considerations:
For validation I chose to be strict on input. 
The reason - I believe when doing orders - the mistakes should be highlighted instead of tried to guess what to do automatically.
And for efficient use the sorting is pretty much achieved at the point of preparation - by a unified key naming convention.
Keys then can be easily sorted and that then becomes the order at which things need to be picked.
