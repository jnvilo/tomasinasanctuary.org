<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 
?>
<div id="rokweather">
	
    <div class="leftbit">
        <div class="icon">
            <?php if ($params->get('enable_icon')==1) : ?>
            <img src="<?php echo $weather->icon; ?>" alt="" />
            <?php endif; ?>
            <div class="degf"><?php echo $weather->current_temp_f;?>&deg;</div>
            <div class="degc"><?php echo $weather->current_temp_c;?>&deg;</div>
        </div>
        <div class="degrees"><span class="active">&deg;F</span> | <span>&deg;C</span></div>
    </div>
    
    
    <div class="content">
        <div class="location">
        <form action="<?php echo str_replace("&", "&amp;", $url); ?>" method="post">
        <input type="text" name="weather_location" value="<?php echo $weather_location; ?>" />  
        <input type="hidden" name="module_name" value="<?php echo $module_name; ?>" />  
        </form>
        </div>
		<div class="rokweather-wrapper">
	        <?php if (isset($weather->error)) :?>
	        <div class="row error"><?php echo $weather->error; ?></div>    
	        <?php else: ?>
	        <div class="row"><?php echo $weather->current_condition; ?></div>
	        <?php if ($params->get('enable_humidity')==1) : ?>
	        <div class="row"><?php echo $weather->current_humidity; ?></div>
	        <?php endif; ?>
	        <?php if ($params->get('enable_wind')==1) : ?>
	        <div class="row"><?php echo $weather->current_wind; ?></div>
	        <?php endif; ?>
	        <?php if ($params->get('enable_forecast')==1): ?>
	        <div class="forecast">

			<?php
				$weather->forecast = array_slice($weather->forecast, 0, $params->get('forcast_show', 4));
			?>
		
	            <?php foreach ($weather->forecast as $day): ?>
	            <div class="day">
	                <span><?php echo $day['day_of_week']; ?></span><br />
	                <img src="<?php echo $icon_url."/grey".$day['icon']; ?>" alt="" /><br />
	                <div class="degf">
	                    <span class="low"><?php echo modRokWeatherHelper::getFTemp($day['low'],$weather->units); ?></span> | 
	                    <span class="high"><?php echo modRokWeatherHelper::getFTemp($day['high'],$weather->units); ?></span>
	                </div>
	                <div class="degc">
	                    <span class="low"><?php echo modRokWeatherHelper::getCTemp($day['low'],$weather->units); ?></span> | 
	                    <span class="high"><?php echo modRokWeatherHelper::getCTemp($day['high'],$weather->units); ?></span>
	                </div>
	            </div>    
	            <?php endforeach; ?>
	        </div>
	        <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>