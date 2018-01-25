# Minii Template

**Minii**, is a simple template engine for PHP!

## Install

You can install it using [Composer](https://getcomposer.org/).


	composer require minii-php/minii

## Config

First you need create the config.

    $config = [
    
        'views' => 'app/views',
        'includes' => 'app/views/components',
        'globals' => [
            'root' => 'http://localhost:8000/'
        ]
    ];

- *views* : Is the location of the views.
- *includes* : Is the location of the includes.
- *globals* : Is the global variables.

## Use

    // $... = new Minii\View( [ Config ] );
    $minii = new Minii\View($config);
    
    // $...->render( View , [ Variables ] );
    $minii->render('home',['name'=>'Minii']);

## Render

### Delimiters

- {( $... )} : Use to print a variable.
- {% ... %} : Use to include a component.

**The Minii use HTML files:**

>app/views/home.html

    {% header %}
    
    <a href="{( $root )}">
        <h1>{( $name )}</h1>
    </a>
    
    {% footer %}

**You can use the variables in includes:**

>app/views/components/header.html

    // header include
    
    <header>
        <a href="{( $root )}">
            <span>{( $name )}</span>
        </a>
    <header>