<!DOCTYPE html>
  
<html>
    <head>
        <title>my blog</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous"> 
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
        <link rel="stylesheet" href="basecss.css" type="text/css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Imperial+Script&display=swap" rel="stylesheet">
    </head>
    <body>
        <header class="main-header">
            <nav class="first-nav">
                <ul class="main-nav" id="menu1">
                    <li><a href="#about-me">Обо мне</a></li>
                    <li><a href="#main-contacts">Контакты</a></li>
                    <li><a href="#cooperation">Сотрудничество</a></li>
                </ul>
            </nav>
        </header>
        <div class="main-video">
            <video id="background-video" autoplay muted loop>
              <source src="video.MP4" type="video/MP4">
              <source src="video.webm" type="video/webm">
            </video>
        </div>
        <div class="brand">
            <div class="brand-up">Victoria Skvortsova</div>
            <div class="brand-down">my blog</div>
        </div>
        <div class="container">
            <nav class="second-nav">
                <ul class="main-second-nav" id="menu2">
                    <li><a href="#about-me">Обо мне</a></li>
                    <li><a href="#main-contacts">Контакты</a></li>
                    <li><a href="#cooperation">Сотрудничество</a></li>
                </ul>
            </nav>
        </div>
        <div class="stripe-one"></div>
        <div class="container-fluid">
            <div class="about-me">Коротко обо мне</div>
            <div class="cartoons">
                <div class="fact-one">
                    <img src="fact-one.jpg" alt="fact-one">
                    <div class="one"><span>Меня зовут Виктория, мне 19 лет. Учусь в КубГУ на программиста (2 курс) по специальности "Прикладная информатика", и я довольно творческая и разносторонняя личность.</span></div>
                </div>
                <div class="fact-two">
                    <div class="two"><span>Я - модель, посещаю модельную школу больше 6 месяцев и принимаю участие в профессиональных съёмках, а также участвовала в модном показе в г. Краснодар. Это моё первое достижение в этой сфере!</span></div>
                    <img src="fact-two.jpg" alt="fact-two">
                </div>
                <div class="fact-three">
                    <img src="fact-three.jpg" alt="fact-three">
                    <div class="three"><span>На данный момент я занимаюсь блогом и активно веду соцсети: Telegram, TikTok, Youtube. Это тоже моё творчество, в которое вкладываю всю свою душу. </span></div>
                </div>
            </div>
            <div class="themes-name">Темы моего блога</div>
            <div class="themes">
                <div class="theme-one">
                    <img class="main-theme" src="lifestyle.jpg" alt="lifestyle">
                    <span>Жизнь</span>
                </div>
                <div class="theme-two">
                    <img class="main-theme" src="beauty.jpg" alt="beauty">
                    <span>Бьюти</span>
                </div>
                <div class="theme-three">
                    <img class="main-theme" src="aesthetic.jpg" alt="aesthetic">
                    <span>Эстетика</span>
                </div>
                <div class="theme-four">
                    <img class="main-theme" src="study.jpg" alt="study">
                    <span>Учёба</span>
                </div>
                <div class="theme-five">
                    <img class="main-theme" src="model.jpg" alt="model">
                    <span>Моделинг</span>
                </div>
            </div>
            <div class="name-slider">Портфолио</div>
            <div class="gallery">
                <div class="multiple">
                    <div class="slide"><img src="1.jpg" alt="1"></div>
                    <div class="slide"><img src="2.jpg" alt="2"></div>
                    <div class="slide"><img src="3.jpg" alt="3"></div>
                    <div class="slide"><img src="4.jpg" alt="4"></div>
                    <div class="slide"><img src="5.jpg" alt="5"></div>
                    <div class="slide"><img src="6.jpg" alt="6"></div>
                    <div class="slide"><img src="7.jpg" alt="7"></div>
                    <div class="slide"><img src="8.jpg" alt="8"></div>
                </div>
            </div>
            <div class="my-cooperations">Бренды, с которыми я сотрудничаю</div>
            <div class="my-brands">
                <img src="semily.jpg" alt="semily">
                <img src="cuteskin.jpg" alt="cuteskin">
            </div>
        </div>
        <footer id="main-contacts">
            <div class="main-footer">
                <div class="main-agree">
                    <div class="request">Оставить заявку на сотрудничество</div>
                    <div class="contacts">
                        <div class="telephone">
                            <img class="phone" src="tel.jpg" alt="phone">8 918 083-92-25
                        </div>
                        <div class="e-mail">
                            <img class="mail" src="mail.jpg" alt="mail">victoriyaskvot@gmail.com
                        </div>
                    </div>
                </div>
                <div class="main-form" id="cooperation">
                    <form id="my-sweet-form" action="https://formcarry.com/s/2Uzb6krMzij" method="POST">
                        <div>
                        <label for="name" class="form-label"></label> 
                        <input class="form-control" id="name" name="name" type="text" placeholder="Ваше имя" autocomplete="name">
                        </div>

                        <div>
                            <label for="email" class="form-label"></label>
                            <input class="form-control" id="email" name="email" type="email" placeholder="Email" autocomplete="email">
                        </div>

                        <div>
                            <label for="name" class="form-label"></label>
                            <input class="form-control" id="tel" name="tel" type="tel" placeholder="Номер телефона" inputmode="numeric">
                        </div>

                        <div>
                            <label for="message" class="form-label"></label>
                            <textarea class="form-control" id="message" name="message" placeholder="Ваш комментарий"></textarea>
                        </div>

                        <div class="main-answer">
                            <input class="form-check-input" type="checkbox" id="checkbox" name="checkbox">
                            <label class="form-check-label" for="checkbox"></label> 
                            <div class="agree">Отправляя заявку, я даю согласие <span class="red-text">на обработку своих персональных данных*</span></div>
                        </div>

                        <div>
                            <button type="submit" class="btn">Отправить</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="stripe-two"></div>
            <div class="end">
                <div class="word-end">Мои соцсети</div>
                <div class="icons">
                    <a href="https://t.me/miviculys"><img class="my-telegram" src="telegram.jpg" alt="telegram"></a>
                    <a href="https://youtube.com/@miviculys?si=jSJeBnrJN9ZZ2K5q"><img class="my-youtube" src="youtube.jpg" alt="youtube"></a>
                    <a href="https://www.tiktok.com/@miviculys?_r=1&_t=ZN-92TDErruiAA"><img class="my-tiktok" src="tiktok.jpg" alt="tiktok"></a>
                </div>
                <div class="word-end">&copy; Скворцова Виктория, 2025 </div>
            </div>
        </footer>
        
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        <script src="javascript1.js"></script>
        <script src="javascript2.js"></script>
        <script src="javascript3.js"></script>
        <script src="https://carrier.formcarry.com/js/v1.js"></script>
    </body>
</html>
