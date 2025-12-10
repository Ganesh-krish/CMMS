<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>portal DrillU - Online Coding and Aptitude Assessments</title>
    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url("/") ?>assets/images/favicons/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url("/") ?>assets/images/favicons/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url("/") ?>assets/images/favicons/favicon-16x16.png" />
    <link rel="manifest" href="<?= base_url("/") ?>assets/images/favicons/site.webmanifest" />
    <meta name="description" content="Eduhive is a very modern and versatile education & online courses HTML Template. It is specially designed for LMS, online courses, education, learning management, e-learning, online training, zoom classes, and all other education related websites and businesses." />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cormorant:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/bootstrap-select/bootstrap-select.min.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/animate/animate.min.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/fontawesome/css/all.min.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/jquery-ui/jquery-ui.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/jarallax/jarallax.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/jquery-magnific-popup/jquery.magnific-popup.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/nouislider/nouislider.min.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/nouislider/nouislider.pips.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/tiny-slider/tiny-slider.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/eduhive-icons/style.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/owl-carousel/css/owl.carousel.min.css" />
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/vendors/owl-carousel/css/owl.theme.default.min.css" />

    <!-- template styles -->
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/css/eduhive.css" />
</head>

<body class="custom-cursor">

    <div class="custom-cursor__cursor"></div>
    <div class="custom-cursor__cursor-two"></div>

    <div class="preloader">
        <div class="preloader__image" style="background-image: url(assets/images/loader.png);"></div>
    </div>
    <!-- /.preloader -->
    <div class="page-wrapper">
        <div class="home-main-header">
            <!-- <div class="topbar">
                <div class="container-fluid">
                    <div class="topbar__inner">
                        <ul class="list-unstyled topbar__info">
                            <li>
                                <span class="topbar__info__icon topbar__info__icon--email">
                                    <i class="icon-email"></i>
                                </span>
                                <a href="mailto:needhelp@company.com">needhelp@company.com</a>
                            </li>
                            <li>
                                <span class="topbar__info__icon">
                                    <i class="icon-telephone"></i>
                                </span>
                                <a href="tel:+6120320024">+61 2032 0024</a>
                            </li>
                            <li>
                                <span class="topbar__info__icon">
                                    <i class="icon-location"></i>
                                </span>
                                <a href="https://www.google.com/maps">2683 Smith Street, Boston - 02110</a>
                            </li>
                        </ul> 
                        <div class="topbar__social social-links">
                            <a href="https://facebook.com">
                                <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                <span class="sr-only">Facebook</span>
                            </a>
                            <a href="https://twitter.com">
                                <i class="icon-twitter" aria-hidden="true"></i>
                                <span class="sr-only">Twitter</span>
                            </a>
                            <a href="https://linkedin.com">
                                <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                <span class="sr-only">Linkedin</span>
                            </a>
                            <a href="https://youtube.com">
                                <i class="fab fa-youtube" aria-hidden="true"></i>
                                <span class="sr-only">Youtube</span>
                            </a>
                        </div> 
                    </div>
                </div> 
            </div>  -->

            <header class="main-header sticky-header sticky-header--normal">
                <div class="container-fluid">
                    <div class="main-header__inner">
                        <div class="main-header__logo logo-retina">
                            <a href="index.html">
                                <img src="<?= base_url("/") ?>assets/images/logo-dark.png" alt="eduhive HTML" width="209">
                            </a>
                        </div> 
                        <div class="main-header__right">
                            <nav class="main-header__nav main-menu">
                                <ul class="main-menu__list one-page-scroll-menu">
                                    <li class="dropdown megamenu scrollToLink current">
                                        <a href="#home">Home</a> 
                                    </li>
                                    <li class="scrollToLink"><a href="#about">About</a></li>
                                    <li class="scrollToLink"><a href="#courses">Courses</a></li>
                                    <li class="scrollToLink"><a href="#instructors">Instructors</a></li>
                                    <li class="scrollToLink"><a href="#testimonials">Testimonials</a></li>
                                    <li class="scrollToLink"><a href="#blog">Blog</a></li>
                                </ul>
                            </nav> 
                            <div class="mobile-nav__btn mobile-nav__toggler">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div> 
                             
                            <!-- <a href="contact.html" class="main-header__btn eduhive-btn eduhive-btn--border">
                                <span>Sign Up</span>
                                <span class="eduhive-btn__icon">
                                    <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                </span>
                            </a>  -->
                            <a href="contact.html" class="main-header__btn eduhive-btn eduhive-btn--border">
                                <span>Log In</span>
                                <span class="eduhive-btn__icon">
                                    <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                </span>
                            </a> 
                        </div> 
                    </div> 
                </div> 
            </header>
        </div> 

        <section class="main-slider-one" id="home">
            <div class="main-slider-one__carousel eduhive-owl__carousel eduhive-owl__carousel--basic-nav owl-carousel owl-theme" data-owl-options='{
                "items": 1,
                "margin": 0,
                "animateIn": "fadeIn",
                "animateOut": "fadeOut",
                "loop": true,
                "smartSpeed": 1000,
                "nav": false,
                "dots": false,
                "autoplay": true,
                "navText": ["<span class=\"icon-arrow-left\"></span>","<span class=\"icon-arrow-right\"></span>"]
            }'>
                <div class="main-slider-one__item">
                    <div class="container">
                        <div class="row gutter-y-60 align-items-center">
                            <div class="main-slider-one__col-content">
                                <div class="main-slider-one__content">
                                    <img src="<?= base_url("/") ?>assets/images/shapes/main-slider-shape-1-1.png" alt="shape" class="main-slider-one__content__shape slider-image">
                                    <p class="main-slider-one__sub-title">The Pathway to Education</p><!-- /.sub-title -->
                                    <h2 class="main-slider-one__title">
                                        Learn New <span class="main-slider-one__title__shape">Skills Online</span> <br>
                                        With Top <span class="main-slider-one__title__text">instructors</span>
                                    </h2><!-- /.title -->
                                    <div class="main-slider-one__description">
                                        <p class="main-slider-one__text">There Are Many Variations Of Passages Of Lorem Ipsum Available, But The Majority Have Suffered Alteration In Some.</p><!-- /.text -->
                                    </div><!-- /.description -->
                                    <div class="main-slider-one__button">
                                        <a href="courses.html" class="main-slider-one__btn-1 eduhive-btn">
                                            <span>find course</span>
                                            <span class="eduhive-btn__icon">
                                                <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                            </span>
                                        </a><!-- /.eduhive-btn -->
                                        <a href="about.html" class="main-slider-one__btn-2 eduhive-btn eduhive-btn--border">
                                            <span>About us</span>
                                            <span class="eduhive-btn__icon">
                                                <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                            </span>
                                        </a><!-- /.eduhive-btn -->
                                    </div><!-- /.button -->
                                </div><!-- /.main-slider-one__content -->
                            </div><!-- /.main-slider-one__col-content -->
                            <div class="main-slider-one__col-image">
                                <div class="main-slider-one__image">
                                    <div class="main-slider-one__image__left">
                                        <div class="main-slider-one__image__one">
                                            <div class="main-slider-one__image__one__inner">
                                                <img src="<?= base_url("/") ?>assets/images/main-slider/main-slider-1-1.jpg" alt="slider image" class="slider-image">
                                            </div><!-- /.main-slider-one__image__one__inner -->
                                            <div class="total-student">
                                                <div class="total-student__inner">
                                                    <div class="total-student__image">
                                                        <img src="<?= base_url("/") ?>assets/images/main-slider/main-slider-student-1-1.png" alt="student" class="slider-image">
                                                        <img src="<?= base_url("/") ?>assets/images/main-slider/main-slider-student-1-2.png" alt="student" class="slider-image">
                                                    </div><!-- /.total-student__image -->
                                                    <h4 class="total-student__text count-box">
                                                        <span class="count-text" data-stop="200" data-speed="1500">0</span><span>k+ <br> Students</span>
                                                    </h4><!-- /.total-student__text -->
                                                </div><!-- /.total-student__inner -->
                                            </div><!-- /.total-student -->
                                        </div><!-- /.main-slider-one__image__one -->
                                    </div><!-- /.main-slider-one__image__left -->
                                    <div class="main-slider-one__image__right">
                                        <div class="main-slider-one__image__two">
                                            <img src="<?= base_url("/") ?>assets/images/main-slider/main-slider-1-2.jpg" alt="slider image" class="slider-image">
                                        </div><!-- /.main-slider-one__image__two -->
                                        <div class="main-slider-one__image__three">
                                            <img src="<?= base_url("/") ?>assets/images/main-slider/main-slider-1-3.jpg" alt="slider image" class="slider-image">
                                        </div><!-- /.main-slider-one__image__three -->
                                    </div><!-- /.main-slider-one__image__right -->
                                    <img src="<?= base_url("/") ?>assets/images/shapes/main-slider-shape-1-3.png" alt="shape" class="main-slider-one__image__shape-one slider-image">
                                    <img src="<?= base_url("/") ?>assets/images/shapes/main-slider-shape-1-4.png" alt="shape" class="main-slider-one__image__shape-two slider-image">
                                    <img src="<?= base_url("/") ?>assets/images/shapes/main-slider-shape-1-5.png" alt="shape" class="main-slider-one__image__shape-three slider-image">
                                    <div class="main-slider-one__image__shape-four"></div><!-- /.main-slider-one__image__shape -->
                                </div><!-- /.main-slider-one__image -->
                            </div><!-- /.main-slider-one__col-image -->
                        </div><!-- /.row gutter-y-60 -->
                    </div><!-- /.container -->
                    <div class="main-slider-one__shape-one"></div><!-- /.main-slider-one__shape-one -->
                    <div class="main-slider-one__shape-two"></div><!-- /.main-slider-one__shape-two -->
                    <div class="main-slider-one__shape-three"></div><!-- /.main-slider-one__shape-three -->
                    <img src="<?= base_url("/") ?>assets/images/shapes/main-slider-shape-1-2.png" alt="shape" class="main-slider-one__shape-four slider-image">
                </div><!-- /.main-slider-one__item -->
                <div class="main-slider-one__item">
                    <div class="container">
                        <div class="row gutter-y-60 align-items-center">
                            <div class="main-slider-one__col-content">
                                <div class="main-slider-one__content">
                                    <img src="<?= base_url("/") ?>assets/images/shapes/main-slider-shape-1-1.png" alt="shape" class="main-slider-one__content__shape slider-image">
                                    <p class="main-slider-one__sub-title">The Pathway to Education</p><!-- /.sub-title -->
                                    <h2 class="main-slider-one__title">
                                        Learn <span class="main-slider-one__title__shape">New Skills</span> To Go <br>
                                        Ahead For Your <span class="main-slider-one__title__text">Career</span>
                                    </h2><!-- /.title -->
                                    <div class="main-slider-one__description">
                                        <p class="main-slider-one__text">Who's Responsible For The Ask For This Request? My Supervisor Didn't Like The Latest Revision You Gave Me Can You Switch Back.</p><!-- /.text -->
                                    </div><!-- /.description -->
                                    <div class="main-slider-one__button">
                                        <a href="courses.html" class="main-slider-one__btn-1 eduhive-btn">
                                            <span>find course</span>
                                            <span class="eduhive-btn__icon">
                                                <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                            </span>
                                        </a><!-- /.eduhive-btn -->
                                        <a href="about.html" class="main-slider-one__btn-2 eduhive-btn eduhive-btn--border">
                                            <span>About us</span>
                                            <span class="eduhive-btn__icon">
                                                <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                            </span>
                                        </a><!-- /.eduhive-btn -->
                                    </div><!-- /.button -->
                                </div><!-- /.main-slider-one__content -->
                            </div><!-- /.main-slider-one__col-content -->
                            <div class="main-slider-one__col-image">
                                <div class="main-slider-one__image">
                                    <div class="main-slider-one__image__left">
                                        <div class="main-slider-one__image__one">
                                            <div class="main-slider-one__image__one__inner">
                                                <img src="<?= base_url("/") ?>assets/images/main-slider/main-slider-1-4.jpg" alt="slider image" class="slider-image">
                                            </div><!-- /.main-slider-one__image__one__inner -->
                                            <div class="total-student">
                                                <div class="total-student__inner">
                                                    <div class="total-student__image">
                                                        <img src="<?= base_url("/") ?>assets/images/main-slider/main-slider-student-1-1.png" alt="student" class="slider-image">
                                                        <img src="<?= base_url("/") ?>assets/images/main-slider/main-slider-student-1-2.png" alt="student" class="slider-image">
                                                    </div><!-- /.total-student__image -->
                                                    <h4 class="total-student__text count-box">
                                                        <span class="count-text" data-stop="200" data-speed="1500">0</span><span>k+ <br> Students</span>
                                                    </h4><!-- /.total-student__text -->
                                                </div><!-- /.total-student__inner -->
                                            </div><!-- /.total-student -->
                                        </div><!-- /.main-slider-one__image__one -->
                                    </div><!-- /.main-slider-one__image__left -->
                                    <div class="main-slider-one__image__right">
                                        <div class="main-slider-one__image__two">
                                            <img src="<?= base_url("/") ?>assets/images/main-slider/main-slider-1-5.jpg" alt="slider image" class="slider-image">
                                        </div><!-- /.main-slider-one__image__two -->
                                        <div class="main-slider-one__image__three">
                                            <img src="<?= base_url("/") ?>assets/images/main-slider/main-slider-1-6.jpg" alt="slider image" class="slider-image">
                                        </div><!-- /.main-slider-one__image__three -->
                                    </div><!-- /.main-slider-one__image__right -->
                                    <img src="<?= base_url("/") ?>assets/images/shapes/main-slider-shape-1-3.png" alt="shape" class="main-slider-one__image__shape-one slider-image">
                                    <img src="<?= base_url("/") ?>assets/images/shapes/main-slider-shape-1-4.png" alt="shape" class="main-slider-one__image__shape-two slider-image">
                                    <img src="<?= base_url("/") ?>assets/images/shapes/main-slider-shape-1-5.png" alt="shape" class="main-slider-one__image__shape-three slider-image">
                                    <div class="main-slider-one__image__shape-four"></div><!-- /.main-slider-one__image__shape -->
                                </div><!-- /.main-slider-one__image -->
                            </div><!-- /.main-slider-one__col-image -->
                        </div><!-- /.row gutter-y-60 -->
                    </div><!-- /.container -->
                    <div class="main-slider-one__shape-one"></div><!-- /.main-slider-one__shape-one -->
                    <div class="main-slider-one__shape-two"></div><!-- /.main-slider-one__shape-two -->
                    <div class="main-slider-one__shape-three"></div><!-- /.main-slider-one__shape-three -->
                    <img src="<?= base_url("/") ?>assets/images/shapes/main-slider-shape-1-2.png" alt="shape" class="main-slider-one__shape-four slider-image">
                </div><!-- /.main-slider-one__item -->
            </div><!-- /.main-slider-one__carousel -->
        </section><!-- /.main-slider-one -->
 

        <section class="about-one section-space" id="about">
            <div class="about-one__bg" style="background-image: url(assets/images/shapes/about-bg-1-1.png);"></div><!-- /.about-one__bg -->
            <div class="container">
                <div class="row gutter-y-50 align-items-center">
                    <div class="col-lg-6 wow fadeInLeft" data-wow-duration="1500ms">
                        <div class="about-one__image">
                            <div class="about-one__image__one">
                                <img src="<?= base_url("/") ?>assets/images/about/about-1-1.jpg" alt="about">
                            
                            </div><!-- /.about-one__image__one -->
                            <div class="about-one__image__two">
                                <img src="<?= base_url("/") ?>assets/images/about/about-1-2.jpg" alt="about">
                            </div><!-- /.about-one__image__two -->
                            <img src="<?= base_url("/") ?>assets/images/shapes/about-shape-1-1.png" alt="about" class="about-one__image__shape">
                            <div class="about-one__image__circle">
                                <div class="about-one__image__circle__inner"></div>
                            </div><!-- /.about-one__image__circle -->
                        </div><!-- /.about-one__image -->
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <div class="about-one__content">
                            <div class="sec-title wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="00ms">
                                <h6 class="sec-title__tagline">about drillu</h6><!-- /.sec-title__tagline -->
                                <h3 class="sec-title__title">we are the most popular online <br> <span class="sec-title__title__shape">skill development</span> <span class="sec-title__title__text">platform</span></h3><!-- /.sec-title__title -->
                            </div><!-- /.sec-title -->
                            <div class="about-one__description wow fadeInUp" data-wow-duration="1500ms">
                                <p class="about-one__text">DrillU is a powerful platform designed to help students prepare for campus interviews and build their careers with confidence. It provides personalized support and tools to ensure students achieve their professional goals.
                                    Created by a team of 20 experienced placement professionals, along with corporate trainers and academicians, DrillU offers real-world insights and practical training that match industry needs.
                                    With expert-designed question banks, mock interviews, mentorship, and placement opportunities, DrillU supports students, placement officers, and colleges in achieving placement success.
                                    DrillU is more than just a preparation tool—it's a partner in every student’s career journey, helping them prepare, grow, and succeed in today’s competitive job market.</p><!-- /.about-one__text -->
                            </div><!-- /.about-one__description --> 
                        </div><!-- /.about-one__content -->
                    </div><!-- /.col-lg-6 -->
                </div><!-- /.row gutter-y-50 -->
            </div><!-- /.container -->
        </section><!-- /.about-one section-space -->

        <section class="offer-one section-space-top">
            <div class="container">
                <div class="row gutter-y-60">
                    <div class="col-lg-6 wow fadeInUp" data-wow-duration="1500ms">
                        <div class="offer-one__image">
                            <img src="assets/images/resources/offer-1-1.jpg" alt="offer image" class="offer-one__image__one">
                            <img src="assets/images/resources/offer-1-2.jpg" alt="offer image" class="offer-one__image__two">
                            <img src="assets/images/shapes/offer-shape-1-1.png" alt="offer shape" class="offer-one__image__shape">
                        </div><!-- /.offer-one__image -->
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6">
                        <div class="offer-one__content">
                            <div class="sec-title wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="00ms">
                                <h6 class="sec-title__tagline">Services</h6><!-- /.sec-title__tagline -->
                                <h3 class="sec-title__title"><span class="sec-title__title__shape">We Offer</span> The <span class="sec-title__title__text">Best Carrier</span></h3><!-- /.sec-title__title -->
                            </div><!-- /.sec-title -->
                            <div class="offer-one__inner">
                                <div class="offer-one__item wow fadeInUp" data-wow-duration="1500ms">
                                    <div class="offer-one__item__icon offer-one__item__icon--primary">
                                        <i class="icon-instructors"></i>
                                    </div><!-- /.offer-one__item__icon -->
                                    <div class="offer-one__item__content">
                                        <h4 class="offer-one__item__title">Extensive Practice Resources</h4>
                                        <p class="offer-one__item__text">Access to question papers from 100+ top companies.</p>
                                        <p class="offer-one__item__text">30,000+ questions tailored for campus interviews.</p>
                                        <p class="offer-one__item__text">Regular coding competitions with prizes and placement opportunities</p>
                                    </div><!-- /.offer-one__item__content -->
                                </div><!-- /.offer-one__item -->
                                <div class="offer-one__item wow fadeInUp" data-wow-duration="1500ms">
                                    <div class="offer-one__item__icon offer-one__item__icon--secondary">
                                        <i class="icon-copy-writing"></i>
                                    </div><!-- /.offer-one__item__icon -->
                                    <div class="offer-one__item__content">
                                        <h4 class="offer-one__item__title">Placement Opportunities and Industry Engagement</h4>
                                        <p class="offer-one__item__text">Placement opportunities for up to one year.</p> 
                                        <p class="offer-one__item__text">Direct application to companies recruiting through campus drives.</p> 
                                        <p class="offer-one__item__text">Annual HR conclaves to recognize top performers.</p> 
                                    </div><!-- /.offer-one__item__content -->
                                </div><!-- /.offer-one__item -->
                                <div class="offer-one__item wow fadeInUp" data-wow-duration="1500ms">
                                    <div class="offer-one__item__icon offer-one__item__icon--base">
                                        <i class="icon-community"></i>
                                    </div><!-- /.offer-one__item__icon -->
                                    <div class="offer-one__item__content">
                                        <h4 class="offer-one__item__title">In-Depth Learning Support</h4>
                                        <p class="offer-one__item__text">Textual explanations and coding solutions</p>
                                        <p class="offer-one__item__text">Dedicated mentorship section for expert guidance.</p>
                                    </div><!-- /.offer-one__item__content --> 
                                </div><!-- /.offer-one__item -->
                            </div><!-- /.offer-one__inner -->
                        </div><!-- /.offer-one__content -->
                    </div><!-- /.col-lg-6 -->
                </div><!-- /.row gutter-y-60 -->
            </div><!-- /.container -->
            <div class="offer-one__shape-box"></div><!-- /.offer-one__shape -->
        </section><!-- /.offer-one section-space-top -->


        <section class="courses-one section-space-top2" id="courses">
                <div class="container">
                    <div class="sec-title sec-title--center wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="00ms">
                        <h6 class="sec-title__tagline">our courses</h6><!-- /.sec-title__tagline -->
                        <h3 class="sec-title__title"><span>Our</span> <span class="sec-title__title__shape">Most</span> <span>Popular</span> <span class="sec-title__title__text">Courses</span></h3><!-- /.sec-title__title -->
                    </div><!-- /.sec-title -->
                </div><!-- /.container -->
                <div class="courses-one__container container">
                    <div class="courses-one__carousel eduhive-owl__carousel--with-shadow eduhive-owl__carousel eduhive-owl__carousel--basic-nav owl-carousel owl-theme" data-owl-options='{
                "items": 1,
                "margin": 10,
                "loop": true,
                "smartSpeed": 700,
                "rtl": true,
                "nav": true,
                "dots": false,
                "navText": ["<span class=\"icon-arrow-left\"></span>","<span class=\"icon-arrow-right\"></span>"],
                "autoplay": true,
                "responsive": {
                    "0": {
                        "items": 1,
                        "margin": 10
                    },
                    "768": {
                        "items": 2,
                        "margin": 30
                    },
                    "992": {
                        "items": 3,
                        "margin": 30
                    },
                    "1200": {
                        "items": 3.3,
                        "margin": 30
                    },
                    "1400": {
                        "items": 3.4,
                        "margin": 30
                    },
                    "1536": {
                        "items": 3.5,
                        "margin": 30
                    },
                    "1600": {
                        "items": 3.7,
                        "margin": 30
                    },
                    "1800": {
                        "items": 3.94,
                        "margin": 30
                    }
                }
            }'>
                    <div class="item">
                        <div class="course-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='00ms'>
                            <div class="course-card__image">
                                <img src="<?= base_url("/") ?>assets/images/courses/course-1-1.jpg" alt="WordPress for Everyone: Unlock Your Creativity Online">
                                <div class="course-card__ratings">
                                    <div class="eduhive-ratings">
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                    </div><!-- /.product-ratings -->
                                    <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                </div><!-- /.course-card__ratings -->
                            </div><!-- /.course-card__image -->
                            <div class="course-card__content">
                                <div class="course-card__content__top">
                                    <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                    <div class="course-card__duration">
                                        <span class="course-card__duration__icon">
                                            <i class="icon-clock"></i>
                                        </span><!-- /.course-card__duration__icon -->
                                        25 weeks
                                    </div><!-- /.course-card__duration -->
                                </div><!-- /.course-card__content__top -->
                                <h3 class="course-card__title"><a href="wordpress-development.html">WordPress for Everyone: Unlock Your Creativity Online</a></h3><!-- /.course-card__title -->
                                <div class="course-card__info">
                                    <div class="course-card__lessons">
                                        <span class="course-card__lessons__icon">
                                            <i class="icon-open-book"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        25 lessons
                                    </div><!-- /.course-card__lessons -->
                                    <div class="course-card__students">
                                        <span class="course-card__students__icon">
                                            <i class="icon-multiple-users-silhouette"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        350 Students
                                    </div><!-- /.course-card__students -->
                                </div><!-- /.course-card__info -->
                                <h4 class="course-card__price">$<span>69.00</span></h4><!-- /.course-card__price -->
                            </div><!-- /.course-card__content -->
                            <div class="course-card__hover" style="background-image: url(assets/images/shapes/course-card-bg-1-1.png);">
                                <div class="course-card__hover__content">
                                    <div class="course-card__content__top course-card__content__top--hover">
                                        <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                        <div class="course-card__duration">
                                            <span class="course-card__duration__icon">
                                                <i class="icon-clock"></i>
                                            </span><!-- /.course-card__duration__icon -->
                                            25 weeks
                                        </div><!-- /.course-card__duration -->
                                    </div><!-- /.course-card__content__top -->
                                    <h3 class="course-card__title course-card__title--hover"><a href="wordpress-development.html">WordPress for Everyone: Unlock Your Creativity Online</a></h3><!-- /.course-card__title -->
                                    <p class="course-card__text">Viverra ipsum nunc aliquet bibendum enim facilisis gravida. Diam phasellus vestibulum lorem sed risus</p><!-- /.course-card__text -->
                                    <div class="course-card__ratings course-card__ratings--hover">
                                        <div class="eduhive-ratings">
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                        </div><!-- /.product-ratings -->
                                        <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                    </div><!-- /.course-card__ratings -->
                                    <a href="wordpress-development.html" class="course-card__btn eduhive-btn eduhive-btn--border">
                                        <span>enroll now</span>
                                        <span class="eduhive-btn__icon">
                                            <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                        </span>
                                    </a><!-- /.course-card__btn eduhive-btn -->
                                    <div class="course-card__info course-card__info--hover">
                                        <div class="course-card__lessons">
                                            <span class="course-card__lessons__icon">
                                                <i class="icon-open-book"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            25 lessons
                                        </div><!-- /.course-card__lessons -->
                                        <div class="course-card__students">
                                            <span class="course-card__students__icon">
                                                <i class="icon-multiple-users-silhouette"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            350 Students
                                        </div><!-- /.course-card__students -->
                                    </div><!-- /.course-card__info -->
                                </div><!-- /.course-card__hover__content -->
                            </div><!-- /.course-card__hover -->
                        </div><!-- /.course-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="course-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='100ms'>
                            <div class="course-card__image">
                                <img src="<?= base_url("/") ?>assets/images/courses/course-1-2.jpg" alt="The Data Science Revolution: Upgrading Your Skills">
                                <div class="course-card__ratings">
                                    <div class="eduhive-ratings">
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                    </div><!-- /.product-ratings -->
                                    <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                </div><!-- /.course-card__ratings -->
                            </div><!-- /.course-card__image -->
                            <div class="course-card__content">
                                <div class="course-card__content__top">
                                    <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                    <div class="course-card__duration">
                                        <span class="course-card__duration__icon">
                                            <i class="icon-clock"></i>
                                        </span><!-- /.course-card__duration__icon -->
                                        20 weeks
                                    </div><!-- /.course-card__duration -->
                                </div><!-- /.course-card__content__top -->
                                <h3 class="course-card__title"><a href="data-science.html">The Data Science Revolution: Upgrading Your Skills</a></h3><!-- /.course-card__title -->
                                <div class="course-card__info">
                                    <div class="course-card__lessons">
                                        <span class="course-card__lessons__icon">
                                            <i class="icon-open-book"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        20 lessons
                                    </div><!-- /.course-card__lessons -->
                                    <div class="course-card__students">
                                        <span class="course-card__students__icon">
                                            <i class="icon-multiple-users-silhouette"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        300 Students
                                    </div><!-- /.course-card__students -->
                                </div><!-- /.course-card__info -->
                                <h4 class="course-card__price">$<span>50.00</span></h4><!-- /.course-card__price -->
                            </div><!-- /.course-card__content -->
                            <div class="course-card__hover" style="background-image: url(assets/images/shapes/course-card-bg-1-1.png);">
                                <div class="course-card__hover__content">
                                    <div class="course-card__content__top course-card__content__top--hover">
                                        <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                        <div class="course-card__duration">
                                            <span class="course-card__duration__icon">
                                                <i class="icon-clock"></i>
                                            </span><!-- /.course-card__duration__icon -->
                                            20 weeks
                                        </div><!-- /.course-card__duration -->
                                    </div><!-- /.course-card__content__top -->
                                    <h3 class="course-card__title course-card__title--hover"><a href="data-science.html">The Data Science Revolution: Upgrading Your Skills</a></h3><!-- /.course-card__title -->
                                    <p class="course-card__text">We need to dialog around your choice of work attire we need more paper back of the net quick win yet today</p><!-- /.course-card__text -->
                                    <div class="course-card__ratings course-card__ratings--hover">
                                        <div class="eduhive-ratings">
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                        </div><!-- /.product-ratings -->
                                        <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                    </div><!-- /.course-card__ratings -->
                                    <a href="data-science.html" class="course-card__btn eduhive-btn eduhive-btn--border">
                                        <span>enroll now</span>
                                        <span class="eduhive-btn__icon">
                                            <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                        </span>
                                    </a><!-- /.course-card__btn eduhive-btn -->
                                    <div class="course-card__info course-card__info--hover">
                                        <div class="course-card__lessons">
                                            <span class="course-card__lessons__icon">
                                                <i class="icon-open-book"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            20 lessons
                                        </div><!-- /.course-card__lessons -->
                                        <div class="course-card__students">
                                            <span class="course-card__students__icon">
                                                <i class="icon-multiple-users-silhouette"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            300 Students
                                        </div><!-- /.course-card__students -->
                                    </div><!-- /.course-card__info -->
                                </div><!-- /.course-card__hover__content -->
                            </div><!-- /.course-card__hover -->
                        </div><!-- /.course-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="course-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='200ms'>
                            <div class="course-card__image">
                                <img src="<?= base_url("/") ?>assets/images/courses/course-1-3.jpg" alt="From Zero to Website: A Web Development Adventure">
                                <div class="course-card__ratings">
                                    <div class="eduhive-ratings">
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                    </div><!-- /.product-ratings -->
                                    <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                </div><!-- /.course-card__ratings -->
                            </div><!-- /.course-card__image -->
                            <div class="course-card__content">
                                <div class="course-card__content__top">
                                    <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                    <div class="course-card__duration">
                                        <span class="course-card__duration__icon">
                                            <i class="icon-clock"></i>
                                        </span><!-- /.course-card__duration__icon -->
                                        30 weeks
                                    </div><!-- /.course-card__duration -->
                                </div><!-- /.course-card__content__top -->
                                <h3 class="course-card__title"><a href="web-development.html">From Zero to Website: A Web Development Adventure</a></h3><!-- /.course-card__title -->
                                <div class="course-card__info">
                                    <div class="course-card__lessons">
                                        <span class="course-card__lessons__icon">
                                            <i class="icon-open-book"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        32 lessons
                                    </div><!-- /.course-card__lessons -->
                                    <div class="course-card__students">
                                        <span class="course-card__students__icon">
                                            <i class="icon-multiple-users-silhouette"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        250 Students
                                    </div><!-- /.course-card__students -->
                                </div><!-- /.course-card__info -->
                                <h4 class="course-card__price">$<span>40.00</span></h4><!-- /.course-card__price -->
                            </div><!-- /.course-card__content -->
                            <div class="course-card__hover" style="background-image: url(assets/images/shapes/course-card-bg-1-1.png);">
                                <div class="course-card__hover__content">
                                    <div class="course-card__content__top course-card__content__top--hover">
                                        <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                        <div class="course-card__duration">
                                            <span class="course-card__duration__icon">
                                                <i class="icon-clock"></i>
                                            </span><!-- /.course-card__duration__icon -->
                                            30 weeks
                                        </div><!-- /.course-card__duration -->
                                    </div><!-- /.course-card__content__top -->
                                    <h3 class="course-card__title course-card__title--hover"><a href="web-development.html">From Zero to Website: A Web Development Adventure</a></h3><!-- /.course-card__title -->
                                    <p class="course-card__text">Yet today shall be a cloudy day, thanks to blue sky thinking, we can now deploy our new ui to the cloud</p><!-- /.course-card__text -->
                                    <div class="course-card__ratings course-card__ratings--hover">
                                        <div class="eduhive-ratings">
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                        </div><!-- /.product-ratings -->
                                        <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                    </div><!-- /.course-card__ratings -->
                                    <a href="web-development.html" class="course-card__btn eduhive-btn eduhive-btn--border">
                                        <span>enroll now</span>
                                        <span class="eduhive-btn__icon">
                                            <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                        </span>
                                    </a><!-- /.course-card__btn eduhive-btn -->
                                    <div class="course-card__info course-card__info--hover">
                                        <div class="course-card__lessons">
                                            <span class="course-card__lessons__icon">
                                                <i class="icon-open-book"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            32 lessons
                                        </div><!-- /.course-card__lessons -->
                                        <div class="course-card__students">
                                            <span class="course-card__students__icon">
                                                <i class="icon-multiple-users-silhouette"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            250 Students
                                        </div><!-- /.course-card__students -->
                                    </div><!-- /.course-card__info -->
                                </div><!-- /.course-card__hover__content -->
                            </div><!-- /.course-card__hover -->
                        </div><!-- /.course-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="course-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='00ms'>
                            <div class="course-card__image">
                                <img src="<?= base_url("/") ?>assets/images/courses/course-1-4.jpg" alt="advanced Mastering UI/UX Design Fundamentals course">
                                <div class="course-card__ratings">
                                    <div class="eduhive-ratings">
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                    </div><!-- /.product-ratings -->
                                    <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                </div><!-- /.course-card__ratings -->
                            </div><!-- /.course-card__image -->
                            <div class="course-card__content">
                                <div class="course-card__content__top">
                                    <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                    <div class="course-card__duration">
                                        <span class="course-card__duration__icon">
                                            <i class="icon-clock"></i>
                                        </span><!-- /.course-card__duration__icon -->
                                        15 weeks
                                    </div><!-- /.course-card__duration -->
                                </div><!-- /.course-card__content__top -->
                                <h3 class="course-card__title"><a href="uiux-design.html">advanced Mastering UI/UX Design Fundamentals course</a></h3><!-- /.course-card__title -->
                                <div class="course-card__info">
                                    <div class="course-card__lessons">
                                        <span class="course-card__lessons__icon">
                                            <i class="icon-open-book"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        28 lessons
                                    </div><!-- /.course-card__lessons -->
                                    <div class="course-card__students">
                                        <span class="course-card__students__icon">
                                            <i class="icon-multiple-users-silhouette"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        150 Students
                                    </div><!-- /.course-card__students -->
                                </div><!-- /.course-card__info -->
                                <h4 class="course-card__price">$<span>70.00</span></h4><!-- /.course-card__price -->
                            </div><!-- /.course-card__content -->
                            <div class="course-card__hover" style="background-image: url(assets/images/shapes/course-card-bg-1-1.png);">
                                <div class="course-card__hover__content">
                                    <div class="course-card__content__top course-card__content__top--hover">
                                        <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                        <div class="course-card__duration">
                                            <span class="course-card__duration__icon">
                                                <i class="icon-clock"></i>
                                            </span><!-- /.course-card__duration__icon -->
                                            15 weeks
                                        </div><!-- /.course-card__duration -->
                                    </div><!-- /.course-card__content__top -->
                                    <h3 class="course-card__title course-card__title--hover"><a href="uiux-design.html">advanced Mastering UI/UX Design Fundamentals course</a></h3><!-- /.course-card__title -->
                                    <p class="course-card__text">The closest elephant is the most dangerous. We should have a meeting to discuss the details of the next</p><!-- /.course-card__text -->
                                    <div class="course-card__ratings course-card__ratings--hover">
                                        <div class="eduhive-ratings">
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                        </div><!-- /.product-ratings -->
                                        <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                    </div><!-- /.course-card__ratings -->
                                    <a href="uiux-design.html" class="course-card__btn eduhive-btn eduhive-btn--border">
                                        <span>enroll now</span>
                                        <span class="eduhive-btn__icon">
                                            <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                        </span>
                                    </a><!-- /.course-card__btn eduhive-btn -->
                                    <div class="course-card__info course-card__info--hover">
                                        <div class="course-card__lessons">
                                            <span class="course-card__lessons__icon">
                                                <i class="icon-open-book"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            28 lessons
                                        </div><!-- /.course-card__lessons -->
                                        <div class="course-card__students">
                                            <span class="course-card__students__icon">
                                                <i class="icon-multiple-users-silhouette"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            150 Students
                                        </div><!-- /.course-card__students -->
                                    </div><!-- /.course-card__info -->
                                </div><!-- /.course-card__hover__content -->
                            </div><!-- /.course-card__hover -->
                        </div><!-- /.course-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="course-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='100ms'>
                            <div class="course-card__image">
                                <img src="<?= base_url("/") ?>assets/images/courses/course-1-5.jpg" alt="advanced Mastering graphics Design Fundamentals course">
                                <div class="course-card__ratings">
                                    <div class="eduhive-ratings">
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                    </div><!-- /.product-ratings -->
                                    <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                </div><!-- /.course-card__ratings -->
                            </div><!-- /.course-card__image -->
                            <div class="course-card__content">
                                <div class="course-card__content__top">
                                    <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                    <div class="course-card__duration">
                                        <span class="course-card__duration__icon">
                                            <i class="icon-clock"></i>
                                        </span><!-- /.course-card__duration__icon -->
                                        35 weeks
                                    </div><!-- /.course-card__duration -->
                                </div><!-- /.course-card__content__top -->
                                <h3 class="course-card__title"><a href="graphics-design.html">advanced Mastering graphics Design Fundamentals course</a></h3><!-- /.course-card__title -->
                                <div class="course-card__info">
                                    <div class="course-card__lessons">
                                        <span class="course-card__lessons__icon">
                                            <i class="icon-open-book"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        15 lessons
                                    </div><!-- /.course-card__lessons -->
                                    <div class="course-card__students">
                                        <span class="course-card__students__icon">
                                            <i class="icon-multiple-users-silhouette"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        250 Students
                                    </div><!-- /.course-card__students -->
                                </div><!-- /.course-card__info -->
                                <h4 class="course-card__price">$<span>85.00</span></h4><!-- /.course-card__price -->
                            </div><!-- /.course-card__content -->
                            <div class="course-card__hover" style="background-image: url(assets/images/shapes/course-card-bg-1-1.png);">
                                <div class="course-card__hover__content">
                                    <div class="course-card__content__top course-card__content__top--hover">
                                        <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                        <div class="course-card__duration">
                                            <span class="course-card__duration__icon">
                                                <i class="icon-clock"></i>
                                            </span><!-- /.course-card__duration__icon -->
                                            35 weeks
                                        </div><!-- /.course-card__duration -->
                                    </div><!-- /.course-card__content__top -->
                                    <h3 class="course-card__title course-card__title--hover"><a href="graphics-design.html">advanced Mastering graphics Design Fundamentals course</a></h3><!-- /.course-card__title -->
                                    <p class="course-card__text">The flagpole bazooka that run it past the boss jump right in and banzai attack will they won't they its all greek</p><!-- /.course-card__text -->
                                    <div class="course-card__ratings course-card__ratings--hover">
                                        <div class="eduhive-ratings">
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                        </div><!-- /.product-ratings -->
                                        <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                    </div><!-- /.course-card__ratings -->
                                    <a href="graphics-design.html" class="course-card__btn eduhive-btn eduhive-btn--border">
                                        <span>enroll now</span>
                                        <span class="eduhive-btn__icon">
                                            <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                        </span>
                                    </a><!-- /.course-card__btn eduhive-btn -->
                                    <div class="course-card__info course-card__info--hover">
                                        <div class="course-card__lessons">
                                            <span class="course-card__lessons__icon">
                                                <i class="icon-open-book"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            15 lessons
                                        </div><!-- /.course-card__lessons -->
                                        <div class="course-card__students">
                                            <span class="course-card__students__icon">
                                                <i class="icon-multiple-users-silhouette"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            250 Students
                                        </div><!-- /.course-card__students -->
                                    </div><!-- /.course-card__info -->
                                </div><!-- /.course-card__hover__content -->
                            </div><!-- /.course-card__hover -->
                        </div><!-- /.course-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="course-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='200ms'>
                            <div class="course-card__image">
                                <img src="<?= base_url("/") ?>assets/images/courses/course-1-6.jpg" alt="Digital Marketing course Guideline: Level Up Your Skills">
                                <div class="course-card__ratings">
                                    <div class="eduhive-ratings">
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                    </div><!-- /.product-ratings -->
                                    <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                </div><!-- /.course-card__ratings -->
                            </div><!-- /.course-card__image -->
                            <div class="course-card__content">
                                <div class="course-card__content__top">
                                    <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                    <div class="course-card__duration">
                                        <span class="course-card__duration__icon">
                                            <i class="icon-clock"></i>
                                        </span><!-- /.course-card__duration__icon -->
                                        28 weeks
                                    </div><!-- /.course-card__duration -->
                                </div><!-- /.course-card__content__top -->
                                <h3 class="course-card__title"><a href="digital-marketing.html">Digital Marketing course Guideline: Level Up Your Skills</a></h3><!-- /.course-card__title -->
                                <div class="course-card__info">
                                    <div class="course-card__lessons">
                                        <span class="course-card__lessons__icon">
                                            <i class="icon-open-book"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        26 lessons
                                    </div><!-- /.course-card__lessons -->
                                    <div class="course-card__students">
                                        <span class="course-card__students__icon">
                                            <i class="icon-multiple-users-silhouette"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        100 Students
                                    </div><!-- /.course-card__students -->
                                </div><!-- /.course-card__info -->
                                <h4 class="course-card__price">$<span>20.00</span></h4><!-- /.course-card__price -->
                            </div><!-- /.course-card__content -->
                            <div class="course-card__hover" style="background-image: url(assets/images/shapes/course-card-bg-1-1.png);">
                                <div class="course-card__hover__content">
                                    <div class="course-card__content__top course-card__content__top--hover">
                                        <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                        <div class="course-card__duration">
                                            <span class="course-card__duration__icon">
                                                <i class="icon-clock"></i>
                                            </span><!-- /.course-card__duration__icon -->
                                            28 weeks
                                        </div><!-- /.course-card__duration -->
                                    </div><!-- /.course-card__content__top -->
                                    <h3 class="course-card__title course-card__title--hover"><a href="digital-marketing.html">Digital Marketing course Guideline: Level Up Your Skills</a></h3><!-- /.course-card__title -->
                                    <p class="course-card__text">Unless they bother until the end of time maybe vis a vis too many cooks over the line encourage & support business</p><!-- /.course-card__text -->
                                    <div class="course-card__ratings course-card__ratings--hover">
                                        <div class="eduhive-ratings">
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                        </div><!-- /.product-ratings -->
                                        <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                    </div><!-- /.course-card__ratings -->
                                    <a href="digital-marketing.html" class="course-card__btn eduhive-btn eduhive-btn--border">
                                        <span>enroll now</span>
                                        <span class="eduhive-btn__icon">
                                            <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                        </span>
                                    </a><!-- /.course-card__btn eduhive-btn -->
                                    <div class="course-card__info course-card__info--hover">
                                        <div class="course-card__lessons">
                                            <span class="course-card__lessons__icon">
                                                <i class="icon-open-book"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            26 lessons
                                        </div><!-- /.course-card__lessons -->
                                        <div class="course-card__students">
                                            <span class="course-card__students__icon">
                                                <i class="icon-multiple-users-silhouette"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            100 Students
                                        </div><!-- /.course-card__students -->
                                    </div><!-- /.course-card__info -->
                                </div><!-- /.course-card__hover__content -->
                            </div><!-- /.course-card__hover -->
                        </div><!-- /.course-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="course-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='00ms'>
                            <div class="course-card__image">
                                <img src="<?= base_url("/") ?>assets/images/courses/course-1-7.jpg" alt="The SEO Become an Expert in Search Engine Optimization">
                                <div class="course-card__ratings">
                                    <div class="eduhive-ratings">
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                    </div><!-- /.product-ratings -->
                                    <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                </div><!-- /.course-card__ratings -->
                            </div><!-- /.course-card__image -->
                            <div class="course-card__content">
                                <div class="course-card__content__top">
                                    <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                    <div class="course-card__duration">
                                        <span class="course-card__duration__icon">
                                            <i class="icon-clock"></i>
                                        </span><!-- /.course-card__duration__icon -->
                                        10 weeks
                                    </div><!-- /.course-card__duration -->
                                </div><!-- /.course-card__content__top -->
                                <h3 class="course-card__title"><a href="search-engine-optimization.html">The SEO Become an Expert in Search Engine Optimization</a></h3><!-- /.course-card__title -->
                                <div class="course-card__info">
                                    <div class="course-card__lessons">
                                        <span class="course-card__lessons__icon">
                                            <i class="icon-open-book"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        19 lessons
                                    </div><!-- /.course-card__lessons -->
                                    <div class="course-card__students">
                                        <span class="course-card__students__icon">
                                            <i class="icon-multiple-users-silhouette"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        450 Students
                                    </div><!-- /.course-card__students -->
                                </div><!-- /.course-card__info -->
                                <h4 class="course-card__price">$<span>35.00</span></h4><!-- /.course-card__price -->
                            </div><!-- /.course-card__content -->
                            <div class="course-card__hover" style="background-image: url(assets/images/shapes/course-card-bg-1-1.png);">
                                <div class="course-card__hover__content">
                                    <div class="course-card__content__top course-card__content__top--hover">
                                        <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                        <div class="course-card__duration">
                                            <span class="course-card__duration__icon">
                                                <i class="icon-clock"></i>
                                            </span><!-- /.course-card__duration__icon -->
                                            10 weeks
                                        </div><!-- /.course-card__duration -->
                                    </div><!-- /.course-card__content__top -->
                                    <h3 class="course-card__title course-card__title--hover"><a href="search-engine-optimization.html">The SEO Become an Expert in Search Engine Optimization</a></h3><!-- /.course-card__title -->
                                    <p class="course-card__text">T-shaped individual start procrastinating 2 hours get to do work while procrastinating open book</p><!-- /.course-card__text -->
                                    <div class="course-card__ratings course-card__ratings--hover">
                                        <div class="eduhive-ratings">
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                        </div><!-- /.product-ratings -->
                                        <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                    </div><!-- /.course-card__ratings -->
                                    <a href="search-engine-optimization.html" class="course-card__btn eduhive-btn eduhive-btn--border">
                                        <span>enroll now</span>
                                        <span class="eduhive-btn__icon">
                                            <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                        </span>
                                    </a><!-- /.course-card__btn eduhive-btn -->
                                    <div class="course-card__info course-card__info--hover">
                                        <div class="course-card__lessons">
                                            <span class="course-card__lessons__icon">
                                                <i class="icon-open-book"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            19 lessons
                                        </div><!-- /.course-card__lessons -->
                                        <div class="course-card__students">
                                            <span class="course-card__students__icon">
                                                <i class="icon-multiple-users-silhouette"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            450 Students
                                        </div><!-- /.course-card__students -->
                                    </div><!-- /.course-card__info -->
                                </div><!-- /.course-card__hover__content -->
                            </div><!-- /.course-card__hover -->
                        </div><!-- /.course-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="course-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='100ms'>
                            <div class="course-card__image">
                                <img src="<?= base_url("/") ?>assets/images/courses/course-1-8.jpg" alt="Apps Development Mastering course From Zero to Hero">
                                <div class="course-card__ratings">
                                    <div class="eduhive-ratings">
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                    </div><!-- /.product-ratings -->
                                    <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                </div><!-- /.course-card__ratings -->
                            </div><!-- /.course-card__image -->
                            <div class="course-card__content">
                                <div class="course-card__content__top">
                                    <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                    <div class="course-card__duration">
                                        <span class="course-card__duration__icon">
                                            <i class="icon-clock"></i>
                                        </span><!-- /.course-card__duration__icon -->
                                        40 weeks
                                    </div><!-- /.course-card__duration -->
                                </div><!-- /.course-card__content__top -->
                                <h3 class="course-card__title"><a href="apps-development.html">Apps Development Mastering course From Zero to Hero</a></h3><!-- /.course-card__title -->
                                <div class="course-card__info">
                                    <div class="course-card__lessons">
                                        <span class="course-card__lessons__icon">
                                            <i class="icon-open-book"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        37 lessons
                                    </div><!-- /.course-card__lessons -->
                                    <div class="course-card__students">
                                        <span class="course-card__students__icon">
                                            <i class="icon-multiple-users-silhouette"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        400 Students
                                    </div><!-- /.course-card__students -->
                                </div><!-- /.course-card__info -->
                                <h4 class="course-card__price">$<span>55.00</span></h4><!-- /.course-card__price -->
                            </div><!-- /.course-card__content -->
                            <div class="course-card__hover" style="background-image: url(assets/images/shapes/course-card-bg-1-1.png);">
                                <div class="course-card__hover__content">
                                    <div class="course-card__content__top course-card__content__top--hover">
                                        <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                        <div class="course-card__duration">
                                            <span class="course-card__duration__icon">
                                                <i class="icon-clock"></i>
                                            </span><!-- /.course-card__duration__icon -->
                                            40 weeks
                                        </div><!-- /.course-card__duration -->
                                    </div><!-- /.course-card__content__top -->
                                    <h3 class="course-card__title course-card__title--hover"><a href="apps-development.html">Apps Development Mastering course From Zero to Hero</a></h3><!-- /.course-card__title -->
                                    <p class="course-card__text">Baseline the procedure and samepage your department i am dead inside, and move the needle, so exposing new</p><!-- /.course-card__text -->
                                    <div class="course-card__ratings course-card__ratings--hover">
                                        <div class="eduhive-ratings">
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                        </div><!-- /.product-ratings -->
                                        <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                    </div><!-- /.course-card__ratings -->
                                    <a href="apps-development.html" class="course-card__btn eduhive-btn eduhive-btn--border">
                                        <span>enroll now</span>
                                        <span class="eduhive-btn__icon">
                                            <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                        </span>
                                    </a><!-- /.course-card__btn eduhive-btn -->
                                    <div class="course-card__info course-card__info--hover">
                                        <div class="course-card__lessons">
                                            <span class="course-card__lessons__icon">
                                                <i class="icon-open-book"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            37 lessons
                                        </div><!-- /.course-card__lessons -->
                                        <div class="course-card__students">
                                            <span class="course-card__students__icon">
                                                <i class="icon-multiple-users-silhouette"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            400 Students
                                        </div><!-- /.course-card__students -->
                                    </div><!-- /.course-card__info -->
                                </div><!-- /.course-card__hover__content -->
                            </div><!-- /.course-card__hover -->
                        </div><!-- /.course-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="course-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='200ms'>
                            <div class="course-card__image">
                                <img src="<?= base_url("/") ?>assets/images/courses/course-1-9.jpg" alt="Web Design Wizardry Mastering Responsive Website">
                                <div class="course-card__ratings">
                                    <div class="eduhive-ratings">
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                        <span class="eduhive-ratings__icon">
                                            <i class="fa fa-star"></i>
                                        </span><!-- /.eduhive-ratings__icon -->
                                    </div><!-- /.product-ratings -->
                                    <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                </div><!-- /.course-card__ratings -->
                            </div><!-- /.course-card__image -->
                            <div class="course-card__content">
                                <div class="course-card__content__top">
                                    <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                    <div class="course-card__duration">
                                        <span class="course-card__duration__icon">
                                            <i class="icon-clock"></i>
                                        </span><!-- /.course-card__duration__icon -->
                                        26 weeks
                                    </div><!-- /.course-card__duration -->
                                </div><!-- /.course-card__content__top -->
                                <h3 class="course-card__title"><a href="web-design.html">Web Design Wizardry Mastering Responsive Website</a></h3><!-- /.course-card__title -->
                                <div class="course-card__info">
                                    <div class="course-card__lessons">
                                        <span class="course-card__lessons__icon">
                                            <i class="icon-open-book"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        18 lessons
                                    </div><!-- /.course-card__lessons -->
                                    <div class="course-card__students">
                                        <span class="course-card__students__icon">
                                            <i class="icon-multiple-users-silhouette"></i>
                                        </span><!-- /.course-card__lessons__icon -->
                                        80 Students
                                    </div><!-- /.course-card__students -->
                                </div><!-- /.course-card__info -->
                                <h4 class="course-card__price">$<span>28.00</span></h4><!-- /.course-card__price -->
                            </div><!-- /.course-card__content -->
                            <div class="course-card__hover" style="background-image: url(assets/images/shapes/course-card-bg-1-1.png);">
                                <div class="course-card__hover__content">
                                    <div class="course-card__content__top course-card__content__top--hover">
                                        <div class="course-card__category">Experts</div><!-- /.course-card__category -->
                                        <div class="course-card__duration">
                                            <span class="course-card__duration__icon">
                                                <i class="icon-clock"></i>
                                            </span><!-- /.course-card__duration__icon -->
                                            26 weeks
                                        </div><!-- /.course-card__duration -->
                                    </div><!-- /.course-card__content__top -->
                                    <h3 class="course-card__title course-card__title--hover"><a href="web-design.html">Web Design Wizardry Mastering Responsive Website</a></h3><!-- /.course-card__title -->
                                    <p class="course-card__text">Race without a finish line we don't want to boil the ocean. What do you feel you would bring to the table</p><!-- /.course-card__text -->
                                    <div class="course-card__ratings course-card__ratings--hover">
                                        <div class="eduhive-ratings">
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                            <span class="eduhive-ratings__icon">
                                                <i class="fa fa-star"></i>
                                            </span><!-- /.eduhive-ratings__icon -->
                                        </div><!-- /.product-ratings -->
                                        <p class="course-card__ratings__text">5 Ratings</p><!-- /.course-card__ratings__text -->
                                    </div><!-- /.course-card__ratings -->
                                    <a href="web-design.html" class="course-card__btn eduhive-btn eduhive-btn--border">
                                        <span>enroll now</span>
                                        <span class="eduhive-btn__icon">
                                            <span class="eduhive-btn__icon__inner"><i class="icon-right-arrow"></i></span>
                                        </span>
                                    </a><!-- /.course-card__btn eduhive-btn -->
                                    <div class="course-card__info course-card__info--hover">
                                        <div class="course-card__lessons">
                                            <span class="course-card__lessons__icon">
                                                <i class="icon-open-book"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            18 lessons
                                        </div><!-- /.course-card__lessons -->
                                        <div class="course-card__students">
                                            <span class="course-card__students__icon">
                                                <i class="icon-multiple-users-silhouette"></i>
                                            </span><!-- /.course-card__lessons__icon -->
                                            80 Students
                                        </div><!-- /.course-card__students -->
                                    </div><!-- /.course-card__info -->
                                </div><!-- /.course-card__hover__content -->
                            </div><!-- /.course-card__hover -->
                        </div><!-- /.course-card -->
                    </div><!-- /.item -->
                </div><!-- /.courses-one__carousel -->
            </div><!-- /.courses-one__container container -->
            <img src="<?= base_url("/") ?>assets/images/shapes/courses-shape-1-1.png" alt="shape" class="courses-one__shape">
        </section><!-- /.courses-one section-space-top2 -->

        <section class="funfact-one section-space-bottom funfact-one--home">
            <div class="funfact-one__inner">
                <div class="funfact-one__bg" style="background-image: url(assets/images/shapes/funfact-bg-1-1.png);"></div>
            </div><!-- /.funfact-one__inner -->
            <div class="container">
                <div class="funfact-one__grid">
                    <div class="funfact-one__item funfact-one__item--secondary wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="00ms">
                        <div class="funfact-one__icon">
                            <span class="funfact-one__icon__inner"><i class="icon-connectibity"></i></span>
                        </div><!-- /.funfact-one__icon -->
                        <h3 class="funfact-one__title count-box">
                            <span class="count-text" data-stop="30" data-speed="1500">0</span>
                            <span>k+</span>
                        </h3><!-- /.funfact-one__title -->
                        <p class="funfact-one__text">Satisfied Student</p><!-- /.funfact-one__text -->
                    </div><!-- /.funfact-one__item -->
                    <div class="funfact-one__item funfact-one__item--primary wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="100ms">
                        <div class="funfact-one__icon">
                            <span class="funfact-one__icon__inner"><i class="icon-batch-assign"></i></span>
                        </div><!-- /.funfact-one__icon -->
                        <h3 class="funfact-one__title count-box">
                            <span class="count-text" data-stop="6500" data-speed="1500">0</span>
                            <span>+</span>
                        </h3><!-- /.funfact-one__title -->
                        <p class="funfact-one__text">Class Completed</p><!-- /.funfact-one__text -->
                    </div><!-- /.funfact-one__item -->
                    <div class="funfact-one__item funfact-one__item--primary wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="200ms">
                        <div class="funfact-one__icon">
                            <span class="funfact-one__icon__inner"><i class="icon-students"></i></span>
                        </div><!-- /.funfact-one__icon -->
                        <h3 class="funfact-one__title count-box">
                            <span class="count-text" data-stop="6561" data-speed="1500">0</span>
                            <span>+</span>
                        </h3><!-- /.funfact-one__title -->
                        <p class="funfact-one__text">Active Students</p><!-- /.funfact-one__text -->
                    </div><!-- /.funfact-one__item -->
                    <div class="funfact-one__item funfact-one__item--secondary wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="300ms">
                        <div class="funfact-one__icon">
                            <span class="funfact-one__icon__inner"><i class="icon-instructors"></i></span>
                        </div><!-- /.funfact-one__icon -->
                        <h3 class="funfact-one__title count-box">
                            <span class="count-text" data-stop="400" data-speed="1500">0</span>
                            <span>+</span>
                        </h3><!-- /.funfact-one__title -->
                        <p class="funfact-one__text">Experts Instructors</p><!-- /.funfact-one__text -->
                    </div><!-- /.funfact-one__item -->
                </div><!-- /.rfunfact-one__grid -->
            </div><!-- /.container -->
        </section><!-- /.funfact-one section-space-bottom -->

        <section class="faq-one section-space">
            <div class="container">
                <div class="row gutter-y-50">
                    <div class="col-lg-6">
                        <div class="faq-one__content">
                            <div class="sec-title wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="00ms">
                                <h6 class="sec-title__tagline">faq’s</h6><!-- /.sec-title__tagline -->
                                <h3 class="sec-title__title">We Are Always Ensure <span class="sec-title__title__text">Best <br> Course</span> For <span class="sec-title__title__shape">Your Learning</span></h3><!-- /.sec-title__title -->
                            </div><!-- /.sec-title -->
                            <div class="faq-one__accordion">
                                <div class="eduhive-accordion" data-grp-name="eduhive-accordion">
                                    <div class="accordion wow fadeInUp" data-wow-duration="1500ms">
                                        <div class="accordion-title">
                                            <h4>How long should a business plan be?</h4>
                                            <span class="accordion-title__icon">
                                                <i class="icon-double-arrow"></i>
                                            </span>
                                        </div><!-- /.accordion-title -->
                                        <div class="accordion-content">
                                            <div class="inner">
                                                <p>Bring to the table win-win survival strategies to ensure proactive domination. At the end of the day, going forward, a new normal.</p>
                                            </div><!-- /.inner -->
                                        </div><!-- /.accordion-content -->
                                    </div><!-- /.accordion-item -->
                                    <div class="accordion active wow fadeInUp" data-wow-duration="1500ms">
                                        <div class="accordion-title">
                                            <h4>What is included in your services?</h4>
                                            <span class="accordion-title__icon">
                                                <i class="icon-double-arrow"></i>
                                            </span>
                                        </div><!-- /.accordion-title -->
                                        <div class="accordion-content">
                                            <div class="inner">
                                                <p>There are many variations of passages Lorem Ipsum but the majority have suffered alteration in some form, by injected humour.</p>
                                            </div><!-- /.inner -->
                                        </div><!-- /.accordion-content -->
                                    </div><!-- /.accordion-item -->
                                    <div class="accordion wow fadeInUp" data-wow-duration="1500ms">
                                        <div class="accordion-title">
                                            <h4>What type of company is measured?</h4>
                                            <span class="accordion-title__icon">
                                                <i class="icon-double-arrow"></i>
                                            </span>
                                        </div><!-- /.accordion-title -->
                                        <div class="accordion-content">
                                            <div class="inner">
                                                <p>Prior to joining company, she spent 20+ years at Inmosys, where he held a wide range of global leadership roles, from services to products.</p>
                                            </div><!-- /.inner -->
                                        </div><!-- /.accordion-content -->
                                    </div><!-- /.accordion-item -->
                                </div><!-- /.faq-accordion -->
                            </div><!-- /.faq-one__accordion -->
                        </div><!-- /.faq-one__content -->
                    </div><!-- /.col-lg-6 -->
                    <div class="col-lg-6 wow fadeInRight" data-wow-duration="1500ms">
                        <div class="faq-one__image">
                            <div class="faq-one__image__one">
                                <div class="faq-one__image__one__inner">
                                    <img src="<?= base_url("/") ?>assets/images/faq/faq-1-1.jpg" alt="faq">
                                </div><!-- /.faq-one__image__one__inner -->
                                <img src="<?= base_url("/") ?>assets/images/shapes/faq-shape-1-2.png" alt="shape" class="faq-one__image__shape-one">
                                <img src="<?= base_url("/") ?>assets/images/shapes/faq-shape-1-3.png" alt="shape" class="faq-one__image__shape-two">
                                <img src="<?= base_url("/") ?>assets/images/shapes/faq-shape-1-5.png" alt="shape" class="faq-one__image__shape-three">
                                <div class="faq-one__image__shape-box"></div><!-- /.faq-one__image__shape-box -->
                            </div><!-- /.faq-one__image__one -->
                            <div class="faq-one__image__two">
                                <img src="<?= base_url("/") ?>assets/images/faq/faq-1-2.jpg" alt="faq">
                            </div><!-- /.faq-one__image__two -->
                        </div><!-- /.faq-one__image -->
                    </div><!-- /.col-lg-6 -->
                </div><!-- /.row gutter-y-50 -->
            </div><!-- /.container -->
            <img src="<?= base_url("/") ?>assets/images/shapes/faq-shape-1-1.png" alt="shape" class="faq-one__shape-one">
            <img src="<?= base_url("/") ?>assets/images/shapes/faq-shape-1-4.png" alt="shape" class="faq-one__shape-two">
        </section><!-- /.faq-one section-space -->

        <section class="instructors-one section-space2" id="instructors">
            <div class="container">
                <div class="sec-title sec-title--center wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="00ms">
                    <h6 class="sec-title__tagline">our expert team</h6><!-- /.sec-title__tagline -->
                    <h3 class="sec-title__title">Our <span class="sec-title__title__text">expert</span> <span class="sec-title__title__shape">instructor</span></h3><!-- /.sec-title__title -->
                </div><!-- /.sec-title -->
                <div class="instructors-one__carousel eduhive-owl__carousel eduhive-owl__carousel--basic-nav owl-carousel owl-theme" data-owl-options='{
			"items": 1,
			"margin": 10,
			"loop": true,
			"smartSpeed": 700,
			"nav": false,
			"dots": true,
			"navText": ["<span class=\"icon-arrow-left\"></span>","<span class=\"icon-arrow-right\"></span>"],
			"autoplay": true,
			"responsive": {
				"0": {
					"items": 1,
					"nav": true,
					"dots": false,
					"margin": 10
				},
				"576": {
					"items": 2,
					"margin": 30
				},
				"992": {
					"items": 3,
					"margin": 30
				},
				"1200": {
					"items": 4,
					"dots": false,
					"margin": 30
				}
			}
		}'>
                    <div class="item">
                        <div class="instructor-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='000ms'>
                            <div class="instructor-card__image">
                                <img src="<?= base_url("/") ?>assets/images/instructors/instructor-1-1.jpg" alt="Mike Hardson">
                                <div class="instructor-card__hover">
                                    <div class="instructor-card__info">
                                        <div class="instructor-card__social">
                                            <span class="instructor-card__social__share">
                                                <i class="icon-share"></i>
                                            </span><!-- /.instructor-card__social__share -->
                                            <div class="instructor-card__social__list">
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Facebook</span>
                                                </a>
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Linkedin</span>
                                                </a>
                                                <a href="https://instagram.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-instagram" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Instagram</span>
                                                </a>
                                            </div><!-- /.instructor-card__social__list -->
                                        </div><!-- /.instructor-card__social -->
                                        <h3 class="instructor-card__name">
                                            <a href="instructor-details.html">Mike Hardson</a>
                                        </h3><!-- /.instructor-card__name -->
                                        <h6 class="instructor-card__designation">data scientist</h6><!-- /.instructor-card__designation -->
                                    </div><!-- /.instructor-card__info -->
                                </div><!-- /.instructor-card__hover -->
                            </div><!-- /.instructor-card__image -->
                        </div><!-- /.instructor-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="instructor-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='100ms'>
                            <div class="instructor-card__image">
                                <img src="<?= base_url("/") ?>assets/images/instructors/instructor-1-2.jpg" alt="aleesha brown">
                                <div class="instructor-card__hover">
                                    <div class="instructor-card__info">
                                        <div class="instructor-card__social">
                                            <span class="instructor-card__social__share">
                                                <i class="icon-share"></i>
                                            </span><!-- /.instructor-card__social__share -->
                                            <div class="instructor-card__social__list">
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Facebook</span>
                                                </a>
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Linkedin</span>
                                                </a>
                                                <a href="https://instagram.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-instagram" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Instagram</span>
                                                </a>
                                            </div><!-- /.instructor-card__social__list -->
                                        </div><!-- /.instructor-card__social -->
                                        <h3 class="instructor-card__name">
                                            <a href="instructor-details.html">aleesha brown</a>
                                        </h3><!-- /.instructor-card__name -->
                                        <h6 class="instructor-card__designation">Web Developer</h6><!-- /.instructor-card__designation -->
                                    </div><!-- /.instructor-card__info -->
                                </div><!-- /.instructor-card__hover -->
                            </div><!-- /.instructor-card__image -->
                        </div><!-- /.instructor-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="instructor-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='200ms'>
                            <div class="instructor-card__image">
                                <img src="<?= base_url("/") ?>assets/images/instructors/instructor-1-3.jpg" alt="david cooper">
                                <div class="instructor-card__hover">
                                    <div class="instructor-card__info">
                                        <div class="instructor-card__social">
                                            <span class="instructor-card__social__share">
                                                <i class="icon-share"></i>
                                            </span><!-- /.instructor-card__social__share -->
                                            <div class="instructor-card__social__list">
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Facebook</span>
                                                </a>
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Linkedin</span>
                                                </a>
                                                <a href="https://instagram.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-instagram" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Instagram</span>
                                                </a>
                                            </div><!-- /.instructor-card__social__list -->
                                        </div><!-- /.instructor-card__social -->
                                        <h3 class="instructor-card__name">
                                            <a href="instructor-details.html">david cooper</a>
                                        </h3><!-- /.instructor-card__name -->
                                        <h6 class="instructor-card__designation">software engineer</h6><!-- /.instructor-card__designation -->
                                    </div><!-- /.instructor-card__info -->
                                </div><!-- /.instructor-card__hover -->
                            </div><!-- /.instructor-card__image -->
                        </div><!-- /.instructor-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="instructor-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='300ms'>
                            <div class="instructor-card__image">
                                <img src="<?= base_url("/") ?>assets/images/instructors/instructor-1-4.jpg" alt="sarah albert">
                                <div class="instructor-card__hover">
                                    <div class="instructor-card__info">
                                        <div class="instructor-card__social">
                                            <span class="instructor-card__social__share">
                                                <i class="icon-share"></i>
                                            </span><!-- /.instructor-card__social__share -->
                                            <div class="instructor-card__social__list">
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Facebook</span>
                                                </a>
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Linkedin</span>
                                                </a>
                                                <a href="https://instagram.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-instagram" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Instagram</span>
                                                </a>
                                            </div><!-- /.instructor-card__social__list -->
                                        </div><!-- /.instructor-card__social -->
                                        <h3 class="instructor-card__name">
                                            <a href="instructor-details.html">sarah albert</a>
                                        </h3><!-- /.instructor-card__name -->
                                        <h6 class="instructor-card__designation">UI/UX Designer</h6><!-- /.instructor-card__designation -->
                                    </div><!-- /.instructor-card__info -->
                                </div><!-- /.instructor-card__hover -->
                            </div><!-- /.instructor-card__image -->
                        </div><!-- /.instructor-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="instructor-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='00ms'>
                            <div class="instructor-card__image">
                                <img src="<?= base_url("/") ?>assets/images/instructors/instructor-1-5.jpg" alt="kevin martin">
                                <div class="instructor-card__hover">
                                    <div class="instructor-card__info">
                                        <div class="instructor-card__social">
                                            <span class="instructor-card__social__share">
                                                <i class="icon-share"></i>
                                            </span><!-- /.instructor-card__social__share -->
                                            <div class="instructor-card__social__list">
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Facebook</span>
                                                </a>
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Linkedin</span>
                                                </a>
                                                <a href="https://instagram.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-instagram" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Instagram</span>
                                                </a>
                                            </div><!-- /.instructor-card__social__list -->
                                        </div><!-- /.instructor-card__social -->
                                        <h3 class="instructor-card__name">
                                            <a href="instructor-details.html">kevin martin</a>
                                        </h3><!-- /.instructor-card__name -->
                                        <h6 class="instructor-card__designation">App Developer</h6><!-- /.instructor-card__designation -->
                                    </div><!-- /.instructor-card__info -->
                                </div><!-- /.instructor-card__hover -->
                            </div><!-- /.instructor-card__image -->
                        </div><!-- /.instructor-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="instructor-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='100ms'>
                            <div class="instructor-card__image">
                                <img src="<?= base_url("/") ?>assets/images/instructors/instructor-1-6.jpg" alt="christine eve">
                                <div class="instructor-card__hover">
                                    <div class="instructor-card__info">
                                        <div class="instructor-card__social">
                                            <span class="instructor-card__social__share">
                                                <i class="icon-share"></i>
                                            </span><!-- /.instructor-card__social__share -->
                                            <div class="instructor-card__social__list">
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Facebook</span>
                                                </a>
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Linkedin</span>
                                                </a>
                                                <a href="https://instagram.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-instagram" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Instagram</span>
                                                </a>
                                            </div><!-- /.instructor-card__social__list -->
                                        </div><!-- /.instructor-card__social -->
                                        <h3 class="instructor-card__name">
                                            <a href="instructor-details.html">christine eve</a>
                                        </h3><!-- /.instructor-card__name -->
                                        <h6 class="instructor-card__designation">SEO Expert</h6><!-- /.instructor-card__designation -->
                                    </div><!-- /.instructor-card__info -->
                                </div><!-- /.instructor-card__hover -->
                            </div><!-- /.instructor-card__image -->
                        </div><!-- /.instructor-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="instructor-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='200ms'>
                            <div class="instructor-card__image">
                                <img src="<?= base_url("/") ?>assets/images/instructors/instructor-1-7.jpg" alt="Adlof Carone">
                                <div class="instructor-card__hover">
                                    <div class="instructor-card__info">
                                        <div class="instructor-card__social">
                                            <span class="instructor-card__social__share">
                                                <i class="icon-share"></i>
                                            </span><!-- /.instructor-card__social__share -->
                                            <div class="instructor-card__social__list">
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Facebook</span>
                                                </a>
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Linkedin</span>
                                                </a>
                                                <a href="https://instagram.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-instagram" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Instagram</span>
                                                </a>
                                            </div><!-- /.instructor-card__social__list -->
                                        </div><!-- /.instructor-card__social -->
                                        <h3 class="instructor-card__name">
                                            <a href="instructor-details.html">Adlof Carone</a>
                                        </h3><!-- /.instructor-card__name -->
                                        <h6 class="instructor-card__designation">UI/UX Designer</h6><!-- /.instructor-card__designation -->
                                    </div><!-- /.instructor-card__info -->
                                </div><!-- /.instructor-card__hover -->
                            </div><!-- /.instructor-card__image -->
                        </div><!-- /.instructor-card -->
                    </div><!-- /.item -->
                    <div class="item">
                        <div class="instructor-card wow fadeInUp" data-wow-duration='1500ms' data-wow-delay='300ms'>
                            <div class="instructor-card__image">
                                <img src="<?= base_url("/") ?>assets/images/instructors/instructor-1-8.jpg" alt="Sabrina Melton">
                                <div class="instructor-card__hover">
                                    <div class="instructor-card__info">
                                        <div class="instructor-card__social">
                                            <span class="instructor-card__social__share">
                                                <i class="icon-share"></i>
                                            </span><!-- /.instructor-card__social__share -->
                                            <div class="instructor-card__social__list">
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Facebook</span>
                                                </a>
                                                <a href="https://facebook.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Linkedin</span>
                                                </a>
                                                <a href="https://instagram.com">
                                                    <span class="instructor-card__social__list__icon">
                                                        <i class="fab fa-instagram" aria-hidden="true"></i>
                                                    </span>
                                                    <span class="sr-only">Instagram</span>
                                                </a>
                                            </div><!-- /.instructor-card__social__list -->
                                        </div><!-- /.instructor-card__social -->
                                        <h3 class="instructor-card__name">
                                            <a href="instructor-details.html">Sabrina Melton</a>
                                        </h3><!-- /.instructor-card__name -->
                                        <h6 class="instructor-card__designation">Graphics Designer</h6><!-- /.instructor-card__designation -->
                                    </div><!-- /.instructor-card__info -->
                                </div><!-- /.instructor-card__hover -->
                            </div><!-- /.instructor-card__image -->
                        </div><!-- /.instructor-card -->
                    </div><!-- /.item -->
                </div><!-- /.instructors-one__carousel -->
            </div><!-- /.container -->
            <img src="<?= base_url("/") ?>assets/images/shapes/instructors-shape-1-1.png" alt="shape" class="instructors-one__shape-one">
            <img src="<?= base_url("/") ?>assets/images/shapes/instructors-shape-1-2.png" alt="shape" class="instructors-one__shape-two">
            <div class="instructors-one__box-one"></div><!-- /.instructors-one__box-one -->
            <div class="instructors-one__box-two"></div><!-- /.instructors-one__box-two -->
        </section><!-- /.instructors-one section-space2 -->

        <section class="testimonials-one section-space" id="testimonials">
            <div class="container">
                <div class="row gutter-y-50">
                    <div class="col-xl-4">
                        <div class="testimonials-one__content">
                            <div class="sec-title wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="00ms">
                                <h6 class="sec-title__tagline">our testimonials</h6><!-- /.sec-title__tagline -->
                                <h3 class="sec-title__title">peoples <span class="sec-title__title__shape">say about</span> <br> <span class="sec-title__title__text">eduhive</span></h3><!-- /.sec-title__title -->
                            </div><!-- /.sec-title -->
                            <div class="testimonials-one__description wow fadeInUp" data-wow-duration="1500ms">
                                <p class="testimonials-one__text">Aonsectetur adipiscing elit Aenean scelerisque augue vitae consequat Juisque eget congue.</p><!-- /.testimonials-one__text -->
                            </div><!-- /.testimonial-one__description -->
                            <div class="testimonials-one__custome-navs"></div><!-- /.testimonials-one__custome-navs -->
                        </div><!-- /.testimonials-one__content -->
                    </div><!-- /.col-xl-4 -->
                    <div class="col-xl-8">
                        <div class="eduhive-stretch-element-inside-column">
                            <div class="testimonials-one__carousel eduhive-owl__carousel eduhive-owl__carousel--with-shadow owl-theme owl-carousel" data-owl-options='{
                        "items": 1,
                        "margin": 30,
                        "smartSpeed": 700,
                        "loop": true,
                        "autoplay": 600,
                        "nav": true,
                        "navContainer": ".testimonials-one__custome-navs",
                        "dots": false,
                        "navText": ["<span class=\"icon-arrow-left\"></span>","<span class=\"icon-arrow-right\"></span>"],
                        "responsive":{
                            "0":{
                                "items": 1,
                                "margin": 10
                            },
                            "576":{
                                "items": 1.5
                            },
                            "768":{
                                "items": 1.8
                            },
                            "992":{
                                "items": 2.6
                            },
                            "1200":{
                                "items": 2.3
                            },
                            "1360":{
                                "items": 2.3
                            },
                            "1536":{
                                "items": 2.5
                            },
                            "1600":{
                                "items": 2.7
                            },
                            "1800":{
                                "items": 2.94
                            }
                        }
                        }'>
                                <div class="item wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="00ms">
                                    <div class="testimonial-card">
                                        <div class="testimonial-card__top">
                                            <div class="testimonial-card__image">
                                                <img src="<?= base_url("/") ?>assets/images/testimonials/testimonial-1-1.jpg" alt="Esther Howard">
                                                <span class="testimonial-card__icon"><i class="icon-quote-2"></i></span><!-- /.testimonial-card__icon -->
                                            </div><!-- /.testimonial-card__image -->
                                            <div class="testimonial-card__identity">
                                                <h5 class="testimonial-card__name">Esther Howard</h5>
                                                <p class="testimonial-card__designation">Ethical Hacker</p>
                                            </div><!-- /.testimonial-card__identity -->
                                        </div><!-- /.testimonial-card__top -->
                                        <div class="testimonial-card__content">
                                            <p class="testimonial-card__quote">I think this should be fairly easy so if you just want to have a look what you've given us is texty, we want sexy. Labrador you might wanna give it</p><!-- /.testimonial-card__quote -->
                                            <div class="eduhive-ratings">
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                            </div><!-- /.product-ratings -->
                                        </div><!-- /.testimonial-card__content -->
                                    </div><!-- /.testimonial-card -->
                                </div><!-- /.owl-slide-item-->
                                <div class="item wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="100ms">
                                    <div class="testimonial-card">
                                        <div class="testimonial-card__top">
                                            <div class="testimonial-card__image">
                                                <img src="<?= base_url("/") ?>assets/images/testimonials/testimonial-1-2.jpg" alt="Sarah Albert">
                                                <span class="testimonial-card__icon"><i class="icon-quote-2"></i></span><!-- /.testimonial-card__icon -->
                                            </div><!-- /.testimonial-card__image -->
                                            <div class="testimonial-card__identity">
                                                <h5 class="testimonial-card__name">Sarah Albert</h5>
                                                <p class="testimonial-card__designation">managing director</p>
                                            </div><!-- /.testimonial-card__identity -->
                                        </div><!-- /.testimonial-card__top -->
                                        <div class="testimonial-card__content">
                                            <p class="testimonial-card__quote">Can you make pink a little more pinkish can you remove my double chin on my business card photo? i don't like the way it looks can you</p><!-- /.testimonial-card__quote -->
                                            <div class="eduhive-ratings">
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                            </div><!-- /.product-ratings -->
                                        </div><!-- /.testimonial-card__content -->
                                    </div><!-- /.testimonial-card -->
                                </div><!-- /.owl-slide-item-->
                                <div class="item wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="200ms">
                                    <div class="testimonial-card">
                                        <div class="testimonial-card__top">
                                            <div class="testimonial-card__image">
                                                <img src="<?= base_url("/") ?>assets/images/testimonials/testimonial-1-3.jpg" alt="Kevin Martin">
                                                <span class="testimonial-card__icon"><i class="icon-quote-2"></i></span><!-- /.testimonial-card__icon -->
                                            </div><!-- /.testimonial-card__image -->
                                            <div class="testimonial-card__identity">
                                                <h5 class="testimonial-card__name">Kevin Martin</h5>
                                                <p class="testimonial-card__designation">managing director</p>
                                            </div><!-- /.testimonial-card__identity -->
                                        </div><!-- /.testimonial-card__top -->
                                        <div class="testimonial-card__content">
                                            <p class="testimonial-card__quote">I really like the colour but can you change it, yet is there a way we can make the page feel more introductory without being cheesy can't you</p><!-- /.testimonial-card__quote -->
                                            <div class="eduhive-ratings">
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                                <span class="eduhive-ratings__icon">
                                                    <i class="fa fa-star"></i>
                                                </span><!-- /.eduhive-ratings__icon -->
                                            </div><!-- /.product-ratings -->
                                        </div><!-- /.testimonial-card__content -->
                                    </div><!-- /.testimonial-card -->
                                </div><!-- /.owl-slide-item-->
                            </div><!-- /.testimonials-one__carousel -->
                        </div><!-- /.eduhive-stretch-element-inside-column -->
                    </div><!-- /.col-xl-8 -->
                </div><!-- /.row gutter-y-50 -->
            </div><!-- /.container -->
            <img src="<?= base_url("/") ?>assets/images/shapes/testimonials-shape-1-1.png" alt="shape" class="testimonials-one__shape">
            <div class="testimonials-one__shape-box"></div><!-- /.testimonials-one__shape-box -->
        </section><!-- /.testimonials-one section-space -->  

        <footer class="main-footer section-space-top">
            <div class="container">
                <div class="main-footer__top">
                    <a href="index.html" class="main-footer__logo">
                        <img src="<?= base_url("/") ?>assets/images/logo-dark.png" width="209" alt="Eduhive HTML Template">
                    </a><!-- /.main-footer__logo -->
                    <div class="main-footer__top__inner">
                        <div class="main-footer__newsletter">
                            <form action="#" data-url="MAILCHIMP_FORM_URL" class="main-footer__newsletter__form mc-form">
                                <input type="text" name="EMAIL" placeholder="Enter Your Mail">
                                <button type="submit" class="eduhive-btn">
                                    <span>Get newsletter</span>
                                </button>
                            </form><!-- /.main-footer__newsletter__form mc-form -->
                            <div class="mc-form__response"></div><!-- /.mc-form__response -->
                        </div><!-- /.main-footer__newsletter -->
                        <div class="main-footer__social social-links">
                            <a href="https://facebook.com">
                                <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                <span class="sr-only">Facebook</span>
                            </a>
                            <a href="https://twitter.com">
                                <i class="icon-twitter" aria-hidden="true"></i>
                                <span class="sr-only">Twitter</span>
                            </a>
                            <a href="https://linkedin.com">
                                <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                                <span class="sr-only">Linkedin</span>
                            </a>
                            <a href="https://youtube.com">
                                <i class="fab fa-youtube" aria-hidden="true"></i>
                                <span class="sr-only">Youtube</span>
                            </a>
                        </div><!-- /.main-footer__social social-links -->
                    </div><!-- /.main-footer__top__inner -->
                </div><!-- /.main-footer__top -->
                <div class="row gutter-y-40">
                    <div class="col-xl-3 col-lg-4 wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="00ms">
                        <div class="footer-widget footer-widget--about">
                            <h2 class="footer-widget__title">About Us</h2><!-- /.footer-widget__title -->
                            <p class="footer-widget__about-text">Phasellus ultricies aliquam volutpat
                                ullamcorper laoreet neque, a lacinia
                                curabitur lacinia mollis</p><!-- /.footer-widget__about-text -->
                            <ul class="footer-widget__info">
                                <li>
                                    <span class="footer-widget__info__icon"><i class="icon-location"></i></span>
                                    <a href="https://www.google.com/maps">2683 Smith Street, Boston - 02110</a>
                                </li>
                                <li>
                                    <span class="footer-widget__info__icon"><i class="icon-email"></i></span>
                                    <a href="mailto:needhelp@company.com">needhelp@company.com</a>
                                </li>
                                <li>
                                    <span class="footer-widget__info__icon"><i class="icon-telephone"></i></span>
                                    <a href="tel:+6120320024">+61 2032 0024</a>
                                </li>
                            </ul><!-- /.footer-widget__info -->
                        </div><!-- /.footer-widget -->
                    </div><!-- /.col-xl-3 col-lg-4 -->
                    <div class="col-xl-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="100ms">
                        <div class="footer-widget footer-widget--links">
                            <h2 class="footer-widget__title">Quick Link</h2><!-- /.footer-widget__title -->
                            <ul class="list-unstyled footer-widget__links">
                                <li>
                                    <a href="about.html">
                                        <span class="footer-widget__links__icon">
                                            <i class="icon-double-arrow"></i>
                                        </span>
                                        About Eduhive
                                    </a>
                                </li>
                                <li>
                                    <a href="courses.html">
                                        <span class="footer-widget__links__icon">
                                            <i class="icon-double-arrow"></i>
                                        </span>
                                        Our Courses
                                    </a>
                                </li>
                                <li>
                                    <a href="instructors.html">
                                        <span class="footer-widget__links__icon">
                                            <i class="icon-double-arrow"></i>
                                        </span>
                                        Instructors
                                    </a>
                                </li>
                                <li>
                                    <a href="instructor-details.html">
                                        <span class="footer-widget__links__icon">
                                            <i class="icon-double-arrow"></i>
                                        </span>
                                        Instructor Details
                                    </a>
                                </li>
                                <li>
                                    <a href="contact.html">
                                        <span class="footer-widget__links__icon">
                                            <i class="icon-double-arrow"></i>
                                        </span>
                                        Contact Us
                                    </a>
                                </li>
                            </ul><!-- /.list-unstyled footer-widget__links -->
                        </div><!-- /.footer-widget -->
                    </div><!-- /.col-xl-3 col-md-4 col-sm-6 -->
                    <div class="col-xl-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="200ms">
                        <div class="footer-widget footer-widget--links">
                            <h2 class="footer-widget__title">Courses</h2><!-- /.footer-widget__title -->
                            <ul class="list-unstyled footer-widget__links">
                                <li>
                                    <a href="apps-development.html">
                                        <span class="footer-widget__links__icon">
                                            <i class="icon-double-arrow"></i>
                                        </span>
                                        Apps Development
                                    </a>
                                </li>
                                <li>
                                    <a href="digital-marketing.html">
                                        <span class="footer-widget__links__icon">
                                            <i class="icon-double-arrow"></i>
                                        </span>
                                        Digital Marketing
                                    </a>
                                </li>
                                <li>
                                    <a href="graphics-design.html">
                                        <span class="footer-widget__links__icon">
                                            <i class="icon-double-arrow"></i>
                                        </span>
                                        Graphics Design
                                    </a>
                                </li>
                                <li>
                                    <a href="uiux-design.html">
                                        <span class="footer-widget__links__icon">
                                            <i class="icon-double-arrow"></i>
                                        </span>
                                        UI/UX Design
                                    </a>
                                </li>
                                <li>
                                    <a href="data-science.html">
                                        <span class="footer-widget__links__icon">
                                            <i class="icon-double-arrow"></i>
                                        </span>
                                        Data Science
                                    </a>
                                </li>
                            </ul><!-- /.list-unstyled footer-widget__links -->
                        </div><!-- /.footer-widget -->
                    </div><!-- /.col-xl-3 col-md-4 col-sm-6 -->
                    <div class="col-xl-3 col-md-4 wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="300ms">
                        <div class="footer-widget footer-widget--gallery">
                            <h2 class="footer-widget__title">Our Gallery</h2><!-- /.footer-widget__title -->
                            <div class="footer-widget__gallery">
                                <a href="gallery.html" class="footer-widget__gallery__link">
                                    <img src="<?= base_url("/") ?>assets/images/gallery/footer-widget-gallery-1.jpg" alt="footer-widget-gallery">
                                    <span class="footer-widget__gallery__icon icon-plus"></span>
                                </a><!-- /.footer-widget__gallery__link -->
                                <a href="gallery.html" class="footer-widget__gallery__link">
                                    <img src="<?= base_url("/") ?>assets/images/gallery/footer-widget-gallery-2.jpg" alt="footer-widget-gallery">
                                    <span class="footer-widget__gallery__icon icon-plus"></span>
                                </a><!-- /.footer-widget__gallery__link -->
                                <a href="gallery.html" class="footer-widget__gallery__link">
                                    <img src="<?= base_url("/") ?>assets/images/gallery/footer-widget-gallery-3.jpg" alt="footer-widget-gallery">
                                    <span class="footer-widget__gallery__icon icon-plus"></span>
                                </a><!-- /.footer-widget__gallery__link -->
                                <a href="gallery.html" class="footer-widget__gallery__link">
                                    <img src="<?= base_url("/") ?>assets/images/gallery/footer-widget-gallery-4.jpg" alt="footer-widget-gallery">
                                    <span class="footer-widget__gallery__icon icon-plus"></span>
                                </a><!-- /.footer-widget__gallery__link -->
                                <a href="gallery.html" class="footer-widget__gallery__link">
                                    <img src="<?= base_url("/") ?>assets/images/gallery/footer-widget-gallery-5.jpg" alt="footer-widget-gallery">
                                    <span class="footer-widget__gallery__icon icon-plus"></span>
                                </a><!-- /.footer-widget__gallery__link -->
                                <a href="gallery.html" class="footer-widget__gallery__link">
                                    <img src="<?= base_url("/") ?>assets/images/gallery/footer-widget-gallery-6.jpg" alt="footer-widget-gallery">
                                    <span class="footer-widget__gallery__icon icon-plus"></span>
                                </a><!-- /.footer-widget__gallery__link -->
                            </div><!-- /.footer-widget__gallery -->
                        </div><!-- /.footer-widget -->
                    </div><!-- /.col-xl-3 col-md-4 -->
                </div><!-- /.row gutter-y-40 -->
            </div><!-- /.container -->
            <div class="main-footer__bottom">
                <div class="container">
                    <div class="main-footer__bottom__inner">
                        <p class="main-footer__copyright">
                            &copy; Copyright <span class="dynamic-year"></span> by Eduhive HTML Template.
                        </p>
                    </div><!-- /.main-footer__inner -->
                </div><!-- /.container -->
            </div><!-- /.main-footer__bottom -->
            <img src="<?= base_url("/") ?>assets/images/shapes/footer-shape-1.png" alt="shape" class="main-footer__shape-one">
            <img src="<?= base_url("/") ?>assets/images/shapes/footer-shape-2.png" alt="shape" class="main-footer__shape-two">
            <img src="<?= base_url("/") ?>assets/images/shapes/footer-shape-3.png" alt="shape" class="main-footer__shape-three">
            <img src="<?= base_url("/") ?>assets/images/shapes/footer-shape-4.png" alt="shape" class="main-footer__shape-four">
            <img src="<?= base_url("/") ?>assets/images/shapes/footer-shape-5.png" alt="shape" class="main-footer__shape-five">
        </footer><!-- /.main-footer section-space-top -->

    </div><!-- /.page-wrapper -->

    <div class="mobile-nav__wrapper">
        <div class="mobile-nav__overlay mobile-nav__toggler"></div><!-- /.mobile-nav__overlay -->
        <div class="mobile-nav__content">
            <span class="mobile-nav__close mobile-nav__toggler"><i class="icon-close"></i></span>
            <div class="logo-box logo-retina">
                <a href="index.html" aria-label="logo image">
                    <img src="<?= base_url("/") ?>assets/images/logo-light.png" width="209" alt="" />
                </a>
            </div><!-- /.logo-box -->
            <div class="mobile-nav__container"></div><!-- /.mobile-nav__container -->
            <ul class="mobile-nav__contact list-unstyled">
                <li>
                    <span class="mobile-nav__contact__icon"><i class="fa fa-envelope"></i></span>
                    <a href="mailto:needhelp@eduhive.com">needhelp@company.com</a>
                </li>
                <li>
                    <span class="mobile-nav__contact__icon"><i class="fa fa-phone-alt"></i></span>
                    <a href="tel:+9156980036420">+91 5698 0036 420</a>
                </li>
            </ul><!-- /.mobile-nav__contact -->
            <div class="mobile-nav__social social-links-two">
                <a href="https://facebook.com">
                    <span class="social-links-two__icon">
                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                    </span><!-- /.social-links-two__icon -->
                    <span class="sr-only">Facebook</span>
                </a>
                <a href="https://twitter.com">
                    <span class="social-links-two__icon">
                        <i class="fab fa-twitter" aria-hidden="true"></i>
                    </span><!-- /.social-links-two__icon -->
                    <span class="sr-only">Twitter</span>
                </a>
                <a href="https://instagram.com">
                    <span class="social-links-two__icon">
                        <i class="fab fa-instagram" aria-hidden="true"></i>
                    </span><!-- /.social-links-two__icon -->
                    <span class="sr-only">Instagram</span>
                </a>
                <a href="https://youtube.com">
                    <span class="social-links-two__icon">
                        <i class="fab fa-youtube" aria-hidden="true"></i>
                    </span><!-- /.social-links-two__icon -->
                    <span class="sr-only">Youtube</span>
                </a>
            </div><!-- /.mobile-nav__social -->
        </div><!-- /.mobile-nav__content -->
    </div><!-- /.mobile-nav__wrapper -->
    <div class="search-popup">
        <div class="search-popup__overlay search-toggler"></div>
        <!-- /.search-popup__overlay -->
        <div class="search-popup__content">
            <form role="search" method="get" class="search-popup__form" action="#">
                <input type="text" id="search" placeholder="Search Here..." />
                <button type="submit" aria-label="search submit" class="eduhive-btn">
                    <i class="icon-search"></i>
                </button>
            </form>
        </div>
        <!-- /.search-popup__content -->
    </div>
    <!-- /.search-popup -->

    <a href="#" data-target="html" class="scroll-to-target scroll-to-top">
        <span class="scroll-to-top__text">back top</span>
        <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
    </a>

    <script src="<?= base_url("/") ?>assets/vendors/jquery/jquery-3.7.0.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/bootstrap-select/bootstrap-select.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/jarallax/jarallax.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/jquery-ui/jquery-ui.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/jquery-ajaxchimp/jquery.ajaxchimp.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/jquery-appear/jquery.appear.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/jquery-circle-progress/jquery.circle-progress.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/jquery-magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/jquery-validate/jquery.validate.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/nouislider/nouislider.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/tiny-slider/tiny-slider.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/wnumb/wNumb.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/owl-carousel/js/owl.carousel.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/owl-carousel/js/owlcarousel2-filter.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/owl-carousel/js/owlcarousel2-progressbar.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/wow/wow.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/imagesloaded/imagesloaded.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/isotope/isotope.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/countdown/countdown.min.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/jquery-circleType/jquery.circleType.js"></script>
    <script src="<?= base_url("/") ?>assets/vendors/jquery-lettering/jquery.lettering.min.js"></script>
    <!-- template js -->
    <script src="<?= base_url("/") ?>assets/js/eduhive.js"></script>
</body>

</html>
