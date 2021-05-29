
## About 

A basic skeleton project template. This runs PHP 8 using the Slim Framework v4, uses Diesel for migrations*, and is designed to be deployed on Heroku. 

Currently, a fair portion of the PHP project structure is influenced by  [odan's slim4-tutorial](https://github.com/odan/slim4-tutorial) (see [his blog post](https://odan.github.io/2019/11/05/slim4-tutorial.html)).


* I don't care that Diesel isn't for PHP. I like the simplicity of just using plain SQL files for handling migrations. Fight me 🤷. 
## Heroku Setup 

1. Create a normal app

2. Add Postgres Addon

```
heroku addons:create heroku-postgresql:hobby-dev
```

3. Add Diesel Buildpack (for running migrations).

```
heroku buildpacks:add --index 1 marcusball/heroku-buildpack-rust
```

If that doesn't work, then use this:

```
heroku buildpacks:add --index 1 https://github.com/marcusball/heroku-buildpack-rust
```

