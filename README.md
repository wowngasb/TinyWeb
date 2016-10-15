# *TinyWEb* [![Build Status](https://travis-ci.org/wowngasb/TinyWeb.svg?branch=master)](https://travis-ci.org/wowngasb/TinyWeb)
```
                                                                                                    
                                                                                                    
      ;7. . :::.. .,;5  7;7;                          ,:::,  .:::   ,.  .,, .           ;77,@,      
      7 5553:. 7555,.@ 5  7@                         78;...@@;.,,604 ,@@5  9@.         ,2   @       
     ;;@477#. 7@777;5@ 7;5@;                          ;7  0@ 7  7@ 77@7 7 @6,           7: @9       
     3@7   7  @7   7@7  :7                             2  25 5  :@ 7@   .@5             5  @        
     64   ;7  @    ;@         .;   .:                  5  7@ 7;  @7@   ,@9       ,.    ,7 7@  :     
          5  8#      :;;;@ :;7,@# ;;,2, 6;;;;@477.;@7  7, .@ ;7  ,@   :8@    .,73,;7   5. @7 ::;7   
          3  @      75  9# 2;  #@87  2@ ;7  @#  7;@4.  77  @  5  @7  :2@    7.7@5: 43  5  @#2   32  
         7: ;@       2  @   5 .@7 5. @2  3  @   4@     ;7  @  3  97 ,7@    7, @  7 7@ ;7 7@; 5  ;@  
         2  @7      .5 ;@  7: @5  5  @   5. #; 4@       3  @. ,  79 ;@.   ;7 ;@;5; 4@ 5  @   7. 98  
        :7  @       7: @5  3  @  ,7 7@   7; 7@7@        3  ## @  .@ @;    3  @357772; 7 7@   3  @.  
        5  9@       2  @  ;7 2@  5  @;   :7  9@         3  ;7@@.  ;@5    .3  @    .  7, @7  :; @8   
       .5  @.      ;7 4@  5  @   5 .@     5  @.         5.  @5 7  8#     ,3  57  3@; 5  @   ; @@    
     57;   774   .77  74;;;  97;7  ;@     7 @:          7: @9  5 #@       5:  ,2@@, ;3  7;;52@4     
     0#490929@   :86992@564994@59944@7    7@;           .2@6   5#@         73##4;    7325#865       
                                        .7@;                                                        
                                     :;; @7                                                         
                                    ;7  @7                                                          
                                    774@7                                                           
                                                                                                    
```

TinyWeb is a Simple PHP Framework based on Composer, looks like [Yaf(Yet Another Framework)](http://www.laruence.com/manual/index.html). 

## Start
### Clone this repository:

```bash
git clone git@github.com:wowngasb/TinyWeb.git
cd TinyWeb
```

### Edit config/config.php from config/config.bak.php:

```bash
cp config/config.bak.php config/config.php
vi config/config.php
```

### Install php dependencies by [composer](https://getcomposer.org/), nodejs dependencies by [npm](https://www.npmjs.com/):

```bash
composer update
cd app
npm install fis3 -g
npm install
```

### Fis3 Release to the public directory:
```bash
cd app
fis3 release -wL
```

### Just run it:
```bash
cd public
php -S 127.0.0.1:3000
```
Visit [http://127.0.0.1:3000/](http://127.0.0.1:3000/)

The index.php is single entry file, so you should add [rewrite](http://www.laruence.com/manual/tutorial.firstpage.html#tutorial.bootstrap)

### It's already running!
<br>
 
## Features

1. MVC architecture, with ORM [illuminate/database](https://packagist.org/packages/illuminate/database)
2. Module bundler: [fex-team/fis3](https://github.com/fex-team/fis3)
3. Debug in Google Chrome with [php-console](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef)
4. Build php API class to javascript and use it with require.
5. Simple [log viewer](http://127.0.0.1/index.php?m=develop&c=syslog&a=index) and API [debug tool](http://127.0.0.1/index.php?m=develop&c=syslog&a=selectapi). (default auth key : dev_123)
## License

The TinyWeb framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
