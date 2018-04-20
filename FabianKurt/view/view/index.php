<div class="dontLook">
</div>
<?php $arr=[]; $base="$this->image";
array_push($arr,$base."https://i.imgur.com/72xjDmY.jpg");
array_push($arr,$base."https://i.imgur.com/72xjDmY.jpg");
array_push($arr,$base."https://i.imgur.com/72xjDmY.jpg")
?>
<div class="slider_wrapper">
    <div id="camera_wrap" class="">
        <?php
            for($camI=0; $camI<count($arr);$camI++){
        ?>
        <div data-src="<?php echo $arr[$camI]?>">
        </div>
        <?php }?>
    </div>
</div>

<div class="content">
  <div class="container_12">
    <div class="grid_12">
      <h2>WELCOME<span>Long Text 1<span class="col1">VIDEOS</span> Long Text 2</span></h2>
<h3><span>SERVICES</span></h3>
    </div>
    <div class="grid_4">
      <div class="icon">
        <img src="<?php echo$this->images?>icon1.png" alt="">
        <div class="title">WATCHING</div>Long Text 3
      </div>
    </div>
    <div class="grid_4">
      <div class="icon">
        <img src="<?php echo$this->images?>icon3.png" alt="">
        <div class="title">UPLOADING</div>upload your own videos
      </div>
    </div>
    <div class="grid_12">
      <h3><span>Trending</span></h3>
    </div>
    <div class="clear"></div>
    <div class="works">
        <?php
        if(isset($this->products[0]['ID'])){
            $products=$this->products;
            $count=6;
            if(count($products)<6)
                $count=count($products);
            $noImg="https://i.imgur.com/72xjDmY.jpg";
            $path=$this->image."products/";
            $dir=__DIR__."/../../../";
            for ($i=0; $i<$count;$i++){
                $image=$noImg;
                if($products[$i]['Image']!=null&&file_exists($dir.$path.$products[$i]['Image']))
                    $image=$path.$products[$i]['Image']
                ?>
                <div class="grid_4">
                    <a href="/video/view/<?php echo$products[$i]['ID']?>" class="gal"><img src="<?php echo$image?>" alt=""></a>
                    <div class="text1 col1 wordBreak"><?php echo$products[$i]['Productname']?></div>
                    <div class="wordBreak"><?php echo$products[$i]['Views']?> Views</div><br>
                    <a href="/video/view/<?php echo$products[$i]['ID']?>">Go to Video</a>
                </div>
        <?php if($i>1 && ($i+1)%3==0)echo"<div class='clear'></div>
";}}else{?>
        <div class="center text1">There are no discount offers available at this moment.</div>
        <?php }?>
    </div>
    <div class="clear"></div>
    <div class="grid_12">
      <h3><span>Testimonials</span></h3></div>
      <div class="grid_6">
        <blockquote>
          <img src="https://png.icons8.com/dotty/128/000000/user-male.png" alt="" class="img_inner fleft">
          <div class="extra_wrapper">
            <p>“Long Text 4”</p>
            <span class="col2 upp">Lisa Smith  </span> - almost a customer
          </div>
        </blockquote>
      </div>
      <div class="grid_6">
        <blockquote>
          <img src="https://png.icons8.com/dotty/128/000000/user-male.png" alt="" class="img_inner fleft">
          <div class="extra_wrapper">
            <p>“Long Text 5”</p>
            <span class="col2 upp">James Bond  </span> - potential investor
          </div>
        </blockquote>
      </div>

    
  </div>
</div>
