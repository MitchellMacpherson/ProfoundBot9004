# ProfoundBot9004

## Todo
This README

## Configuration
See .env/.env.example

## Running
The entry point for CLI is `profoundbot9004.php`

Execution of building a Profound image is based off combining several CLI commands.

- Generating 2 lines of lyrics from ChartLyrics
- Getting a random /r/EarthPorn image from Reddit
- Combining the above 2 results into a generated image
- Posting said image to Facebook

eg. (Bash (Linux/Mac))
```
php profoundbot9004.php image:generate $(php profoundbot9004.php reddit:random) "$(php profoundbot9004.php chartlyrics:random)" && php profoundbot9004.php facebook:post-photo /tmp/yolo.jpg
```

Windows users write your own batch script, and fix the hardcoded `/tmp` dir usage in `ImageGenerator` to use

## Todo
- Fix hardcoded file paths in ImageGenerator and PostImage
