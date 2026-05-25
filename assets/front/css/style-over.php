<?php
/*** set the content type header ***/
header("Content-type: text/css");

$site_color = $_GET['color'];
?>

<?php if (!empty($_GET['font'])): ?>
body{
    font-family: '<?= $_GET['font'] ?>', sans-serif !important;
}

h1,h2,h3,h4,h5,h6{
    font-family: '<?= $_GET['font'] ?>', sans-serif !important;
}
<?php endif ?>

a{
    color:#<?= $_GET['color'] ?>;
    text-decoration:none;
    background-color:transparent
}

a:hover {
    color: #<?= $_GET['color'] ?>;
    text-decoration: none;
}

.btn-primary:not(:disabled):not(.disabled).active, .btn-primary:not(:disabled):not(.disabled):active, .show>.btn-primary.dropdown-toggle {
    color: #fff;
    background-color: #<?= $_GET['color'] ?> !important;
    border-color: #<?= $_GET['color'] ?> !important;
}

.btn-primary:hover {
    color: #fff;
    background-color: #<?= $_GET['color'] ?>;
    border-color: #<?= $_GET['color'] ?>;
}
.btn.btn-primary:hover {
    box-shadow: 0 4px 11px rgba(<?= $_GET['rgb'] ?>,.3);
}
.template-box:hover{
    border-color: #<?= $_GET['color'] ?>;
}
.btn-primary.disabled,.btn-primary:disabled{
    color:#fff;
    background-color:#<?= $_GET['color'] ?>;
    border-color:#<?= $_GET['color'] ?>
}

.btn-outline-primary{
    color:#<?= $_GET['color'] ?>;
    border-color:#<?= $_GET['color'] ?>
}
.btn-outline-primary:hover{
    color:#fff;
    background-color:#<?= $_GET['color'] ?>;
    border-color:#<?= $_GET['color'] ?>
}
.btn-outline-primary.disabled,.btn-outline-primary:disabled{
    color:#<?= $_GET['color'] ?>;
    background-color:transparent
}
.btn-outline-primary:not(:disabled):not(.disabled).active,.btn-outline-primary:not(:disabled):not(.disabled):active,.show>.btn-outline-primary.dropdown-toggle{
    color:#fff;
    background-color:#<?= $_GET['color'] ?>;
    border-color:#<?= $_GET['color'] ?>
}

.btn-link{
    font-weight:500;
    color:#<?= $_GET['color'] ?>;
    text-decoration:none
}

.dropdown-item:focus,.dropdown-item:hover{
    background: rgba(<?= $_GET['rgb'] ?>,.05) !important;
    transition: 0.1s;
    color:#<?= $_GET['color'] ?> !important;
    text-decoration:none;
    background-color:transparent
}
.dropdown-item.active,.dropdown-item:active{
    color:#<?= $_GET['color'] ?>;
    text-decoration:none;
    background-color:transparent
}

.custom-file-input:focus~.custom-file-label{
    border-color:#<?= $_GET['color'] ?>;
    box-shadow:0 0 0 .2rem rgba(0,123,255,.25)
}
.home-search form:hover {
    background: #fff;
    border-radius: 10px;
    box-shadow: rgba(142, 151, 158, 0.15) 0px 4px 19px;
    border: 1px solid #<?= $_GET['color'] ?> !important;
}

.bg-cover-empty {
  position: relative;
  min-height: 250px;
  background-repeat: no-repeat;
  background-position: center;
  -webkit-background-size: cover;
  background-size: cover;
  background: rgba(<?= $_GET['rgb'] ?>,1) !important;
}

.navbar-light .navbar-nav .nav-link:focus,.navbar-light .navbar-nav .nav-link:hover{
    color:#<?= $_GET['color'] ?>;
}
.navbar-light .navbar-nav .nav-link.disabled{
    color:rgba(0,0,0,.3)
}
.navbar-light .navbar-nav .active>.nav-link,.navbar-light .navbar-nav .nav-link.active,.navbar-light .navbar-nav .nav-link.show,.navbar-light .navbar-nav .show>.nav-link{
    color:#<?= $_GET['color'] ?>
}
.navbar-light .navbar-text a{
    color:#<?= $_GET['color'] ?>
}
.navbar-light .navbar-text a:focus,.navbar-light .navbar-text a:hover{
    color:#<?= $_GET['color'] ?>
}

.navbar-dark .navbar-nav .nav-link:focus,.navbar-dark .navbar-nav .nav-link:hover{
    color:#<?= $_GET['color'] ?>
}
.navbar-dark .navbar-nav .active>.nav-link,.navbar-dark .navbar-nav .nav-link.active,.navbar-dark .navbar-nav .nav-link.show,.navbar-dark .navbar-nav .show>.nav-link{
    color:#<?= $_GET['color'] ?>
}

.badge-primary{
    color:#fff;
    background-color:#<?= $_GET['color'] ?>
}

.bg-primary{
    background-color:#<?= $_GET['color'] ?>!important
}

.text-primary{
    color:#<?= $_GET['color'] ?>!important
}

.btn.btn-light-primary{
    background:rgba(190, 190, 190, 0.1);
    color:#<?= $_GET['color'] ?>
}
.btn.btn-light-primary:hover{
    color:#fff;
    background-color:#<?= $_GET['color'] ?>;
    box-shadow:0 4px 11px rgba(40,110,251,.35)
}

.badge-primary-soft{
    background-color: rgba(106,116,123,.1);
    color: #<?= $_GET['color'] ?>;
}
a.badge-primary-soft:focus,a.badge-primary-soft:hover{
    background-color:rgba(106,116,123,.1);
    color:#<?= $_GET['color'] ?>
}

.badge-white-soft.active{
    background-color:#fff;
    color:#<?= $_GET['color'] ?>
}
.badge-white-soft.active:focus,.badge-white-soft.active:hover{
    background-color:#f6f9fc;
    color:#<?= $_GET['color'] ?>
}

.bg-primary::-moz-selection{
    color:#<?= $_GET['color'] ?>;
    background:#fff
}
.bg-primary::selection{
    color:#<?= $_GET['color'] ?>;
    background:#fff
}
.bg-primary{
    color:#<?= $_GET['color'] ?>;
    background:#fff
}

.svg-injector{
    width:auto;
    height:auto;
    fill:none;
    stroke:currentcolor;
    stroke-width:0;
    stroke-linecap:round;
    stroke-linejoin:round;
    color:#<?= $_GET['color'] ?>
}

.breadcrumb .breadcrumb-item a{
    color:#<?= $_GET['color'] ?>
}

::-moz-selection{
    color:#fff;
    background:#<?= $_GET['color'] ?>
}
::selection{
    color:#fff;
    background:#<?= $_GET['color'] ?>
}
::-moz-selection{
    color:#fff;
    background:#<?= $_GET['color'] ?>
}
.navbar-dark .navbar-text a{
    color:#<?= $_GET['color'] ?>
}
.navbar-dark .navbar-text a:focus,.navbar-dark .navbar-text a:hover{
    color:#<?= $_GET['color'] ?>
}

.scroll-to-top{
    font-size:20px;
    text-align:center;
    color: #<?= $_GET['color'] ?>;
    background-color: rgba(190, 190, 190, 0.1);
    text-decoration:none;
    position:fixed;
    bottom:20px;
    right:20px;
    display:none;
    border-radius:50%;
    width:35px;
    height:35px;
    line-height:35px;
    z-index:9999;
    outline:0;
    -webkit-transition:all .3s ease;
    -moz-transition:all .3s ease;
    -o-transition:all .3s ease
}
.scroll-to-top i{
    color:#<?= $_GET['color'] ?>
}
.scroll-to-top:hover{
    color:#fff;
    background:#<?= $_GET['color'] ?>
}
.icon-style-two .icon{
    display:inline-block;
    vertical-align:middle;
    border-radius:50px;
    padding:10px;
    line-height:2.2rem;
    text-align:center;
    background-color:rgba(190, 190, 190, 0.1);
    color:#<?= $_GET['color'] ?>;
    height:3.5rem;
    width:3.5rem
}

.list-style1 i{
    color:#<?= $_GET['color'] ?>;
    font-size:12px;
    background:rgba(190, 190, 190, 0.1);
    border-radius:30px;
    padding:7px;
    line-height:13px
}

.hover-primary:hover{
    color:#<?= $_GET['color'] ?>;
    transition:all .3s ease-in-out
}

.fill-primary{
    fill:#<?= $_GET['color'] ?>
}

.overlay-primary:before{
    background-color:#286efb
}

@media (max-width:991.98px){
    .navbar-dark .navbar-nav .nav-link:hover{
        color:#6a747b;
        color:#<?= $_GET['color'] ?>
    }
    .navbar-dark .navbar-nav .nav-link:focus{
        color:#<?= $_GET['color'] ?>
    }
}


.footer-list-style li a:hover{
    color:#<?= $_GET['color'] ?>
}



.footer-list-style-two li a:hover{
    color:#<?= $_GET['color'] ?>;
    text-decoration: underline;
}

.footer-title-style2:after{
    position:absolute;
    content:'';
    background:#<?= $_GET['color'] ?>;
    width:60px;
    height:2px;
    bottom:2px;
    left:0;
    right:0;
    margin:0 auto
}

.social-icon li a{
    font-size:1.16rem;
    color:#<?= $_GET['color'] ?>
}

.social-icon3 li a{
    width:35px;
    height:35px;
    line-height:35px;
    border:1px solid #<?= $_GET['color'] ?>;
    text-align:center;
    border-radius:4px;
    font-size:15px;
    display:inline-block
}
.social-icon3 li a:hover{
    background-color:#<?= $_GET['color'] ?>;
    color:#fff
}

.tab-style-one .resp-tabs-list li.resp-tab-active{
    border:1px solid #<?= $_GET['color'] ?>;
    border-bottom:none;
    border-color:#<?= $_GET['color'] ?>!important;
    margin-bottom:-1px;
    border-top:4px solid #<?= $_GET['color'] ?>!important;
    border-bottom:0 #fff solid;
    border-bottom:none;
    background-color:#fff;
    color:#<?= $_GET['color'] ?>;
    -ms-border-top-left-radius:5px;
    -webkit-border-top-left-radius:5px;
    -moz-border-top-left-radius:5px;
    -o-border-top-left-radius:5px;
    -ms-border-top-right-radius:5px;
    -webkit-border-top-right-radius:5px;
    -moz-border-top-right-radius:5px;
    -o-border-top-right-radius:5px;
    -ms-border-radius-top-left:5px;
    -webkit-border-radius-top-left:5px;
    -moz-border-radius-top-left:5px;
    -o-border-radius-top-left:5px;
    -ms-border-radius-topright:5px;
    -webkit-border-radius-topright:5px;
    -moz-border-radius-topright:5px;
    -o-border-radius-topright:5px;
    border-top-left-radius:5px;
    border-top-right-radius:5px;
    border-top:none!important;
    border-left:none!important;
    border-right:none!important
}


.tab-style-one .resp-tabs-list li.resp-tab-active:after{
    content:"";
    background:#<?= $_GET['color'] ?>;
    height:1px;
    width:100%;
    position:absolute;
    bottom:-1px;
    left:0;
    margin:0 auto;
    right:0
}

.html-code .copy-clipboard:hover{
    background:#<?= $_GET['color'] ?>;
    color:#fff!important
}

ul.pagination li a {
    background: #f1f1f1;
    padding: 10px 20px;
    color: #<?= $_GET['color'] ?>;
    font-size: 14px;
    font-weight: 500;
    border-radius: 4px;
    margin-right: 4px;
}

ul.pagination li a:hover {
    background:#<?= $_GET['color'] ?>;
    color: #fff !important;
}

.pagination>.active>a{ 
    z-index: 2;
    color: #fff;
    cursor: default;
    background-color: #<?= $_GET['color'] ?> !important;
    border-color: #514adf !important;
}

.icon-plan i {
    background-color: rgba(190, 190, 190, 0.1);
    border-color: #<?= $_GET['color'] ?>;
    font-size: 16px;
    color: #<?= $_GET['color'] ?>;
    padding: 20px;
    border-radius: 50px;
}

.purple .price-tag {
  color: #<?= $_GET['color'] ?>;
}

.ui-state-default:hover {
	background: rgba(190, 190, 190, 0.1);
	color: #<?= $_GET['color'] ?>;
    border: 1px solid #<?= $_GET['color'] ?>;
}

.ui-state-active {
    background: #<?= $_GET['color'] ?>;
    color: #fff;
    border: 1px solid #<?= $_GET['color'] ?>;
}

.ui-state-default.ui-state-active:hover {
    background: #<?= $_GET['color'] ?>;
    color: #fff;
    border: 1px solid #<?= $_GET['color'] ?>;
}



.staff-rdo > input + div:hover{
    border: 2px solid #<?= $_GET['color'] ?>;
}

.staff-rdo > input:checked + div {
    background-color: #fff;
    border: 2px solid #<?= $_GET['color'] ?>;
    border-radius: 4px;
}

.bg-primary-soft{
    background-color: rgba(190, 190, 190, 0.1);
    color: #<?= $_GET['color'] ?>;
}

.ui-timepicker-standard .ui-state-hover {
    border: 1px solid #fff !important;
    background-color: rgba(190, 190, 190, 0.1) !important;
    color: #<?= $_GET['color'] ?> !important;
}

.learn-more{
    color: #<?= $_GET['color'] ?> !important;
    text-decoration: underline;
}

.btn-aceptar{
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 4px !important;
    color: #<?= $_GET['color'] ?> !important;
    border: 1px solid #<?= $_GET['color'] ?> !important;
    background-color: #fff !important;
    font-size: 12px !important;
}

.btn-aceptar:hover{
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 4px !important;
    color: #fff !important;
    border: 1px solid #<?= $_GET['color'] ?> !important;
    background-color: #<?= $_GET['color'] ?> !important;
    font-size: 12px !important;
}

.service-rdo > input + div:hover{
    border: 2px solid #<?= $_GET['color'] ?>;
}

.service-rdo > input{ 
    visibility: hidden; 
    position: absolute;
}

.service-rdo > input:checked + div {
    background-color: #fff;
    border: 2px solid #<?= $_GET['color'] ?>;
    border-radius: 4px;
}

.btn-primary {
    color: #fff;
    background-color: #<?= $_GET['color'] ?> !important;
    border-color: #<?= $_GET['color'] ?> !important;
}

.text-primary {
    color: #<?= $_GET['color'] ?> !important;
}

a h1:hover, a.h1:hover, a h2:hover, a.h2:hover, a h3:hover, a.h3:hover, a h4:hover, a.h4:hover, a h5:hover, a.h5:hover, a h6:hover, a.h6:hover {
    color: #<?= $_GET['color'] ?> !important;
}

.nav-link.text-dark:hover {
    transition: none;
    color: #<?= $_GET['color'] ?> !important;
}

.sidebar-icon i {
    color: #<?= $_GET['color'] ?> !important;
}

.ui-datepicker-header {
    height: 50px;
    line-height: 50px;
    color: #fff;
    background: #<?= $_GET['color'] ?>;
    margin-bottom: 10px;
    font-weight: 500;
    font-size: 18px;
    margin-top: -10px;
    margin-left: -10px;
    margin-right: -10px;
}

.ui-datepicker-calendar thead tr th span {
    display: block;
    width: 40px;
    color: #555;
    margin-bottom: 5px;
    font-size: 14px;
    font-weight: 500;
    margin-left: 15px;

}

.pick-date span {
    color: #<?= $_GET['color'] ?>;
    border-bottom: 1px dotted #2d54de;
}

a.hover:hover {
    transition: none !important;
    color: #<?= $_GET['color'] ?> !important; 
}

.cart-num {
    position: absolute;
    background-color: #<?= $_GET['color'] ?>;
    color: #fff;
    border-radius: 50px;
    width: 20px;
    height: 20px;
    text-align: center;
    display: flex;
    align-items: center;
    text-align: center;
    justify-content: center;
    top: 13px;
    left: 8px;
    font-size: 11px;
    font-style: normal;
}

.company-link {
    transition: none;
    color: #<?= $_GET['color'] ?>;
}

.event_badge {
    position: absolute;
    height: 65px;
    width: 70px;
    background-color: #<?= $_GET['color'] ?>;
    color: #fff;
    top: 10px;
    right: 10px;
    border-radius: 2px;
}

.link-hover:hover{
    color: #<?= $_GET['color'] ?> !important;
    transition: all none !important;
    text-decoration: underline;
}


.template-box:hover .circle-icon {
   background-color: #<?= $_GET['color'] ?> !important;
   color: #fff;
   transition: ease-in-out 0.3s !important;
}

.template-box:hover p.template-box-text {
   color: #<?= $_GET['color'] ?> !important;
}

.text-gradnt{
    background: -webkit-linear-gradient(#<?= $_GET['color'] ?>, #CF9FFF);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}


.nice-select .option:hover, .nice-select .option.focus, .nice-select .option.selected.focus {
    color: #<?= $_GET['color'] ?> !important;
    background: rgba(<?= $_GET['rgb'] ?>,.1) !important;
}

.owl-theme .owl-dots .owl-dot span {
    background: #<?= $_GET['color'] ?> !important;
}

.owlstyle-2:hover {
    background: #<?= $_GET['color'] ?> !important;
    color: #fff;
    padding: 10px 12px;
    border-radius: 50px;
    font-size: 18px;
}

.accordion>.card .btn-link:after {
    background-color: #<?= $_GET['color'] ?> !important;
}

.btn-dark {
    color: #fff;
    background-color: #<?= $_GET['color'] ?> !important;
    border-color: #<?= $_GET['color'] ?> !important;
}

.workflow-img {
    border: 1px solid rgba(<?= $_GET['rgb'] ?>,.1) !important;
}


.template-box.feature:hover{
    border: 1px solid #<?= $_GET['color'] ?>;
    background: rgba(<?= $_GET['rgb'] ?>,.8) !important;
}

.template-box.feature:hover .circle-icon {
   background-color: #fff !important;
   color: #fff;
   transition: ease-in-out 0.3s !important;
}

.template-box.feature:hover p {
   color: #fff !important;
}

.day:hover {
  border: 2px solid rgba(<?= $_GET['rgb'] ?>,.6) !important;
}

.day.active {
  background: rgba(<?= $_GET['rgb'] ?>,.05) !important;
  border: 2px solid rgba(<?= $_GET['rgb'] ?>,.6) !important;
  color: #<?= $_GET['color'] ?>;
}

.day.active p {
  color: #<?= $_GET['color'] ?> !important;
}

.time_btn.active {
    border: 1px solid rgba(<?= $_GET['rgb'] ?>,.9) !important;
    background: rgba(<?= $_GET['rgb'] ?>,.1) !important;
    color: #<?= $_GET['color'] ?> !important;
}

.btn-black:hover {
    color: #fff;
    background-color: rgba(<?= $_GET['rgb'] ?>,.8) !important;
    border-color: rgba(<?= $_GET['rgb'] ?>,.8) !important;
}
