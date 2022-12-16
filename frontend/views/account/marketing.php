<?php

/* @var $this \yii\web\View */


$css = <<<CSS
    ul, #myUL {
      list-style-type: none;
      padding: 0
    }
    .container {
        margin: 0 auto;
        position: relative;
        width: 100%;
        max-width: 1215px;
    }
    *{
      margin: 0;
      padding: 0;
      outline: none;
      box-sizing: border-box;
    }
    @font-face {
    font-family: montseratRegular;
    src:url(/img/mark/fonts/Montserrat-Regular.ttf);
    } 
    @font-face {
    font-family: montseratMedium;
    src:url(/img/mark/fonts/Montserrat-Medium.ttf);
    } 
    @font-face {
    font-family: Montserrat-SemiBold;
    src:url(/img/mark/fonts/Montserrat-SemiBold.ttf);
    } 
    #myUL {
          width: 1133px;
        margin: 20px auto;
        background: #FFFFFF;
        box-shadow: 0px 10px 40px rgb(0 0 0 / 10%);
    }
    
    .caret {
          width: 1%;
      cursor: pointer;
      -webkit-user-select: none; 
      -moz-user-select: none;  
      -ms-user-select: none;  
      user-select: none;
    }
    
    .caret::before {
      content: url('../img/mark/plus.png');
      color: black;
      display: inline-block;
          margin-right: 18px;
        width: 14px;
    }
    
    .caret-down::before {
      content: url('../img/mark/minus.png');
        
    }
    
    .nested {
      display: none;
    }
    
    .active {
      display: block;
    
    }
    #myUL div {
      font-family: montseratRegular;
    padding:31px 25px;
    }
    #myUL li:not(.header) div.level {
       font-family: montseratMedium;
    }
    #myUL div.level:not(.caret){
      padding-left: 89px;
    }
    #myUL div.level {
      width: 21%;
       padding-left: 57px;
     
    }
    #myUL div.email {
      width: 20%
    }
    #myUL div.phone {
      width: 16%
    }
    #myUL div.contract {
      width: 16%
    }
    #myUL div.regData {
      width: 16%
    }
    #myUL div.history button img{
      margin-left: 9px
    }
    #myUL div.history button{
          background: #00FCC5;
        border-radius: 8px;
        border: none;
        padding: 10px 25px;
        outline: none;
        font-size: 12px;
        line-height: 150%;
        font-family: Montserrat-SemiBold
    }
    #myUL li:not(.header) div.history{
       text-align: right;
    }
    #myUL div.history {
      width: 11%;
        padding: 31px 0;
       
    }
    #myUL li ul {
    
      width: 100%
    }
    #myUL li ul li{
      padding: 0
    }
    #myUL li:not(.header) {
          border-top: 1px solid #EEEEEE;
        }
    #myUL li { 
      display: flex;
        flex-wrap: wrap;
        align-items: center;
           
    }
    #myUL li.header {
      display: flex;
      color: #0000EE;
      font-size: 12px;
    line-height: 150%;
      justify-content: space-between;
      align-items: center;
    }
    .marketing {
    
    }
    .marketing .items{
      display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
		gap: 45px 30px;
    }
    
    
    .marketing .items .item{
	   width: 200px;
    }
     
    .marketing .items .item .image{
      height: 240px;
        background: #C4C4C4;
    }
    .marketing .items .item .image:hover .hidden{
      display: flex;
    }
    .marketing .items .item .image .hidden{
      width: 100%;
        height: 100%;
        display: none;
        justify-content: center;
        align-items: center;
    }
    .marketing .items .item .image .hidden a{
          background: #00FCC5;
        border-radius: 8px;
        border: none;
        padding: 10px 32px;
        outline: none;
        font-size: 12px;
        line-height: 150%;
        font-family: Montserrat-SemiBold;
        text-decoration: none;
        color: black;
    }
    .marketing .items .item .image .hidden button{
      background: transparent;
        border: none;
        cursor: pointer;
    }
    .marketing .items .item .name{
      display: flex;
        align-items: flex-end;
         font-family: Montserrat-SemiBold;
         margin-top: 12px;
		 font-size: 14px;
    }
    .marketing .items .item .name img{
      margin-right: 10px
    }
	
CSS;
$this->registerCss($css);
?>


    <section class="privateOffice">

        <div class="container marketing">
            <?= $this->render('account-menu'); ?>
            <div class="items" style="padding-top: 30px;padding-bottom: 90px;">
                <?php foreach ($data as $k=>$v):?>
                    <div class="item">
                        <div class="image" style="background-image: url(<?= Yii::$app->request->hostInfo . $v['img']?>); background-size: cover">
                            <div class="hidden">
                                <a href="<?= \yii\helpers\Url::to([$v['file']])?>" download="" class="download">Скачать</a>
                            </div>
                        </div>
                        <div class="name">
                            <img style="display:none" src="/img/mark/pdf.png" alt="" class="pdf">
                            <p><?= $v['name']?></p>
                        </div>
                    </div>
                <?php endforeach;?>

            </div>
        </div>

    </section>


<?php $js = <<<JS
$(function ( ) {
var toggler = document.getElementsByClassName("caret");
var i;

for (i = 0; i < toggler.length; i++) {
  toggler[i].addEventListener("click", function() { 
    this.parentElement.querySelector(".nested").classList.toggle("active");
    this.classList.toggle("caret-down");
  });
}
})

JS;
$this->registerJs($js);
?>