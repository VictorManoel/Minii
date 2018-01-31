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
    echo $minii->render('home',['name'=>'Minii']);

## Render

**The Minii use HTML files:**

### Load Includes

You may load includes out of the view.

>app/views/components/header.html

    // $...->load( Include , [ Variables ] );
    $header = $minii->load('header',['name'=>'Minii']);

### Delimiters

- {{ $... }} : Use to print a variable.
- {% ... %} : Use to include a component.

>app/views/home.html

    {% header %}
    
    <a href="{{ $root }}">
        <h1>{{ $name }}</h1>
    </a>
    
    {% footer %}

**You can use the variables in includes:**

>app/views/components/header.html

    // header include
    
    <header>
        <a href="{{ $root }}">
            <span>{{ $name }}</span>
        </a>
    <header>

### Control Structures

**If Statements**

You may construct if statements using the *@if*, *@elif*, *@else*, and *@endif* directives.

    @if( $num == 1 )
    
        $num is equal to 1
    
    @elif( $num > 1)
    
        $num is greater than 1
    
    @else
    
        $num is less than 1
    
    @endif

**Loops**

    @for ($i = 0; $i < 10; $i++)
    
        The current value is {{ $i }}
    
    @endfor
