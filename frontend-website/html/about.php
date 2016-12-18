<?php
use \packages\base;
use \packages\base\http;
use \packages\userpanel\date;
use \packages\base\translator;
use \packages\base\frontend\theme;
require_once('header.php');
?>
        <div class="pageTitleArea  animated">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="pageTitle"  id="particles-js">
                            <ul class="pageIndicate">
                                <li><a href="<?php echo base\url(); ?>">صفحه اصلی</a></li>
                                <li><a href="<?php echo base\url("about"); ?>">درباره ما</a></li>
                            </ul>
                            <div class="h2">مای دیزاین</div>
                            <span class="pageTitleBar"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- ** end heroArea **  -->

<!-- ** start about **  -->
    <div class="aboutArea spt90  animated">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="aboutImg"><img src="<?php echo theme::url("img/13.jpg"); ?>" alt=""></div>
                    <div class="aboutContent">
                        <div class="aboutTitle">مای دیزاین</div>
                        <span class="aboutBar"></span>
                        <p>از سال 1388 فعالیت خودمون رو به صورت رسمی آغاز کردیم ، زمان شروع با مشکلات زیادی روبه رو بودیم و به لطف خداوند توانستیم با ارائه سرویس های مخصوص چت روم رضایت مشتریان زیادی را جلب نماییم </p>
                </div>
            </div>
        </div>
    </div>
<!-- ** end about **  -->

<!-- ** start fact **  -->
    <div class="factArea sp90  animated">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="fact">
                        <div class="singleFact">
                            <div class="factDt">
                                <div class="h2 counter">2365</div>
                                <span>چت روم بزرگ و کوچک</span>
                            </div>
                        </div>
                        <div class="singleFact">
                            <div class="factDt">
                                <div class="h2 counter">35489</div>
                                <span>نفر آنلاین بر روی سرور ها</span>
                            </div>
                        </div>
                        <div class="singleFact">
                            <div class="factDt">
                                <div class="h2 counter">209</div>
                                <span>کادر مجرب</span>
                            </div>
                        </div>
                        <div class="singleFact">
                            <div class="factDt">
                                <div class="h2 counter">5000</div>
                                <span>مشتری فعال</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- ** end fact **  -->

<!-- ** start teamArea **  -->
    <div class="teamArea sp90  animated">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="sectionTitle">
                        <span class="h5">درباره تیم</span>
                        <div class="h2">سرگروه های هر تیم</div>
                        <span class="secTitleBar"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-6 animated">
                    <div class="singleTeam">
                        <div class="teamImg">
                            <img src="<?php echo theme::url("img/14.jpg"); ?>" alt="">
                            <ul class="hoverContent">
                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                            </ul>
                        </div>
                        <div class="teamDt">
                            <a href="#">مهدی عابدی</a>
                            <span class="position">برنامه نویس</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 animated">
                    <div class="singleTeam">
                        <div class="teamImg">
                            <img src="<?php echo theme::url("img/15.jpg"); ?>" alt="">
                            <ul class="hoverContent">
                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                            </ul>
                        </div>
                        <div class="teamDt">
                            <a href="#">خانم عابدی</a>
                            <span class="position">طراح و گرافیست</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 animated">
                    <div class="singleTeam">
                        <div class="teamImg">
                            <img src="<?php echo theme::url("img/16.jpg"); ?>" alt="">
                            <ul class="hoverContent">
                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                            </ul>
                        </div>
                        <div class="teamDt">
                            <a href="#">مهدی عابدی</a>
                            <span class="position">برنامه نویس frontend</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 animated">
                    <div class="singleTeam">
                        <div class="teamImg">
                            <img src="<?php echo theme::url("img/17.jpg"); ?>" alt="">
                            <ul class="hoverContent">
                                <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                                <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                            </ul>
                        </div>
                        <div class="teamDt">
                            <a href="#">خانم عابدی</a>
                            <span class="position">پشتیبانی و مشاوره فروش</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- ** end teamArea **  -->

<!-- ** start cta **  -->
    <div class="cta animated">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="ctaContent">
                        <div class="h3 ctaTitle">همین امروز چت روم خودتون رو بسازید</div>
                        <a href="<?php echo base\url('plans'); ?>" class="ctaBtn Btn">شروع سفارش</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- ** end cta **  -->

<?php require_once('footer.php'); ?>
