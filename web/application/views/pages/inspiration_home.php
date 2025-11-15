<div class="NewmainContent" style="display: block; ">
    <p class="newMainTitle">
        <img src="/images/mainStationText.png">
    </p>
    <p class="newMainDescription">
        <span class="newMainDescriptionFirst">TO GET STARTED:</span> 
        <span>Go to the Inspiration Station option in the menu bar above and select an item from the drop down menu.</span>
    </p>
    <span class="spaceStation"><img src="/images/newMainBg.png"></span>
</div>

<script>
    $(function(){
        function blick(){
            if($('.Babs').css('opacity') != 0){
                $('.Babs').animate({opacity: 0}, 1600, function(){
                    blick()
                })
            }else{
                $('.Babs').animate({opacity: .5}, 1600, function(){
                    blick()
                })
            }
        }
        $('.Babs').show().css('opacity',0)
        blick();
    })
</script>