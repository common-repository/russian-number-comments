<?php
/*
Plugin Name: Russian Number Comments
Plugin URI: https://wordpress.org/plugins/russian-number-comments/
Description: Исправляет окончания в комментариях и позволяет переименовать "комментарии" в "отзывы", "отклики", "ответы" и тому подобное.
Version: 2.00
Author: Flector 
Author URI: https://profiles.wordpress.org/flector#content-plugins
Text Domain: russian-number-comments
*/

/*

Для использования активируйте плагин и вставьте в файлы шаблона строчку:

<?php if(function_exists('russian_comments')) { 
    russian_comments('Комментировать', 'комментарий', 'комментариев', 'комментария', 'Комментировать статью &quot;%s&quot;', 'Комментарии закрыты');
} ?>

или краткий вариант со значениями по умолчанию (из настроек плагина):

<?php if(function_exists('russian_comments')) { russian_comments(); } ?>

*/

//проверка версии плагина (запуск функции установки новых опций) begin
function rnc_check_version() {
    $rnc_options = get_option('rnc_options');
    if (!isset($rnc_options['version'])){$rnc_options['version']='1.00';update_option('rnc_options',$rnc_options);}
    if ( $rnc_options['version'] != '2.00' ) {
        rnc_set_new_options();
    }    
}
add_action('plugins_loaded', 'rnc_check_version');
//проверка версии плагина (запуск функции установки новых опций) end

//функция установки новых опций при обновлении плагина у пользователей begin
function rnc_set_new_options() { 
    $rnc_options = get_option('rnc_options');

    //если нет опции при обновлении плагина - записываем ее
    //if (!isset($rnc_options['new_option'])) {$rnc_options['new_option']='value';}
    
    //если необходимо переписать уже записанную опцию при обновлении плагина
    //$rnc_options['old_option'] = 'new_value';
    
    if (!isset($rnc_options['zero'])) {$rnc_options['zero']='Комментировать';}
    if (!isset($rnc_options['one'])) {$rnc_options['one']='комментарий';}
    if (!isset($rnc_options['two'])) {$rnc_options['two']='комментария';}
    if (!isset($rnc_options['more'])) {$rnc_options['more']='комментариев';}
    
    $rnc_options['version'] = '2.00';
    update_option('rnc_options', $rnc_options);
}
//функция установки новых опций при обновлении плагина у пользователей end

//функция установки значений по умолчанию при активации плагина begin
function rnc_init() {
    $rnc_options = array(); 
    $rnc_options['version'] = '2.00';
    
    $rnc_options['zero'] =  'Комментировать';
    $rnc_options['one']  =  'комментарий';
    $rnc_options['two']  =  'комментария';
    $rnc_options['more'] =  'комментариев';

    add_option('rnc_options', $rnc_options);
    
}
add_action('activate_russian-number-comments/russian-number-comments.php', 'rnc_init');
//функция установки значений по умолчанию при активации плагина end

//функция при деактивации плагина begin
function rnc_on_deactivation() {
	if ( ! current_user_can('activate_plugins') ) return;
}
register_deactivation_hook( __FILE__, 'rnc_on_deactivation' );
//функция при деактивации плагина end

//функция при удалении плагина begin
function rnc_on_uninstall() {
	if ( ! current_user_can('activate_plugins') ) return;
    delete_option('rnc_options');
}
register_uninstall_hook( __FILE__, 'rnc_on_uninstall' );
//функция при удалении плагина end

//загрузка файла локализации плагина begin
function rnc_setup(){
    load_plugin_textdomain('russian-number-comments');
}
add_action('init', 'rnc_setup');
//загрузка файла локализации плагина end

//добавление ссылки "Настройки" на странице со списком плагинов begin
function rnc_actions($links) {
	return array_merge(array('settings' => '<a href="options-general.php?page=russian-number-comments.php">' . __('Настройки', 'russian-number-comments') . '</a>'), $links);
}
add_filter('plugin_action_links_' . plugin_basename( __FILE__ ),'rnc_actions');
//добавление ссылки "Настройки" на странице со списком плагинов end

//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина begin
function rnc_files_admin($hook_suffix) {
	$purl = plugins_url('', __FILE__);
    if ( $hook_suffix == 'settings_page_russian-number-comments' ) {
        if(!wp_script_is('jquery')) {wp_enqueue_script('jquery');}
        wp_register_script('rnc-lettering', $purl . '/inc/jquery.lettering.js');  
        wp_enqueue_script('rnc-lettering');
        wp_register_script('rnc-textillate', $purl . '/inc/jquery.textillate.js');  
        wp_enqueue_script('rnc-textillate');
        wp_register_style('rnc-animate', $purl . '/inc/animate.min.css');
        wp_enqueue_style('rnc-animate');
        wp_register_script('rnc-script', $purl . '/inc/rnc-script.js', array(), '2.00');  
        wp_enqueue_script('rnc-script');
        wp_register_style('rnc-css', $purl . '/inc/rnc-css.css', array(), '2.00');
        wp_enqueue_style('rnc-css');
    }
}
add_action('admin_enqueue_scripts', 'rnc_files_admin');
//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина end

//функция вывода страницы настроек плагина begin
function rnc_options_page() {
$purl = plugins_url('', __FILE__);

if (isset($_POST['submit'])) {
     
//проверка безопасности при сохранении настроек плагина begin       
if ( ! wp_verify_nonce( $_POST['rnc_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
   wp_die(__( 'Cheatin&#8217; uh?', 'russian-number-comments' ));
}
//проверка безопасности при сохранении настроек плагина end
        
    //проверяем и сохраняем введенные пользователем данные begin    
    $rnc_options = get_option('rnc_options');
    
    $rnc_options['zero'] = sanitize_text_field($_POST['zero']);
    $rnc_options['one'] = sanitize_text_field($_POST['one']);
    $rnc_options['two'] = sanitize_text_field($_POST['two']);
    $rnc_options['more'] = sanitize_text_field($_POST['more']);
    
    update_option('rnc_options', $rnc_options);
    //проверяем и сохраняем введенные пользователем данные end
}
$rnc_options = get_option('rnc_options');
?>
<?php   if (!empty($_POST) ) :
if ( ! wp_verify_nonce( $_POST['rnc_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
   wp_die(__( 'Cheatin&#8217; uh?', 'russian-number-comments' ));
}
?>
<div id="message" class="updated fade"><p><strong><?php _e('Настройки сохранены.', 'russian-number-comments'); ?></strong></p></div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Настройки плагина &#171;Russian Number Comments&#187;', 'russian-number-comments'); ?></h2>

<div class="metabox-holder" id="poststuff">
<div class="meta-box-sortables">

<div class="postbox">
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode">Вам нравится этот плагин ?</span></h3>
    <div class="inside" style="display: block;margin-right: 12px;">
        <img src="<?php echo $purl . '/img/icon_coffee.png'; ?>" title="Купить мне чашку кофе :)" style=" margin: 5px; float:left;" />
        <p>Привет, меня зовут <strong>Flector</strong>.</p>
        <p>Я потратил много времени на разработку этого плагина.<br />
		Поэтому не откажусь от небольшого пожертвования :)</p>
      <a target="_blank" id="yadonate" href="https://money.yandex.ru/to/41001443750704/200">Подарить</a> 
      <p>Или вы можете заказать у меня услуги по WordPress, от мелких правок до создания полноценного сайта.<br />
        Быстро, качественно и дешево. Прайс-лист смотрите по адресу <a target="new" href="https://www.wpuslugi.ru/?from=rnc-plugin">https://www.wpuslugi.ru/</a>.</p>
        <div style="clear:both;"></div>
    </div>
</div>

<form action="" method="post">

<div class="postbox">

    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e('Настройки', 'russian-number-comments'); ?></span></h3>
    <div class="inside" style="display: block;">

        <table class="form-table">
        
            <tr>
                <th><?php _e('Нет комментариев:', 'russian-number-comments'); ?></th>
                <td>
                    <input type="text" name="zero" size="40" value="<?php echo stripslashes($rnc_options['zero']); ?>" />
                    <br /><small><?php _e('Когда к записи нет комментариев вообще.', 'russian-number-comments'); ?> </small>
                </td>
            </tr>
            <tr>
                <th><?php _e('Один комментарий:', 'russian-number-comments'); ?></th>
                <td>
                    <input type="text" name="one" size="40" value="<?php echo stripslashes($rnc_options['one']); ?>" />
                    <br /><small><?php _e('1 комментар<strong>ий</strong>, 21 комментар<strong>ий</strong> и т.д.', 'russian-number-comments'); ?> </small>
                </td>
            </tr>
            <tr>
                <th><?php _e('Два-четыре комментария:', 'russian-number-comments'); ?></th>
                <td>
                    <input type="text" name="two" size="40" value="<?php echo stripslashes($rnc_options['two']); ?>" />
                    <br /><small><?php _e('2 комментар<strong>ия</strong>, 24 комментар<strong>ия</strong> и т.д.', 'russian-number-comments'); ?> </small>
                </td>
            </tr>
            <tr>
                <th><?php _e('Много комментариев:', 'russian-number-comments'); ?></th>
                <td>
                    <input type="text" name="more" size="40" value="<?php echo stripslashes($rnc_options['more']); ?>" />
                    <br /><small><?php _e('5 комментар<strong>иев</strong>, 25 комментар<strong>иев</strong> и т.д.', 'russian-number-comments'); ?> </small>
                </td>
            </tr>
            

            <tr>
                <th></th>
                <td>
                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Сохранить настройки &raquo;', 'russian-number-comments'); ?>" />
                </td>
            </tr> 
        </table>
    </div>
</div>

<div class="postbox" style="margin-bottom:0;">
    <h3 style="border-bottom: 1px solid #EEE;background: #f7f7f7;"><span class="tcode"><?php _e('О плагине', 'russian-number-comments'); ?></span></h3>
	  <div class="inside" style="padding-bottom:15px;display: block;">
     
      <p><?php _e('Если вам нравится мой плагин, то, пожалуйста, поставьте ему <a target="new" href="https://wordpress.org/plugins/russian-number-comments/"><strong>5 звезд</strong></a> в репозитории.', 'russian-number-comments'); ?></p>
      <p style="margin-top:20px;margin-bottom:10px;"><?php _e('Возможно, что вам также будут интересны другие мои плагины:', 'russian-number-comments'); ?></p>
      
      <div class="about">
        <ul>
            <li><a target="new" href="https://ru.wordpress.org/plugins/rss-for-yandex-zen/">RSS for Yandex Zen</a> - <?php _e('создание RSS-ленты для сервиса Яндекс.Дзен.', 'russian-number-comments'); ?></li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/rss-for-yandex-turbo/">RSS for Yandex Turbo</a> - <?php _e('создание RSS-ленты для сервиса Яндекс.Турбо.', 'russian-number-comments'); ?></li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/bbspoiler/">BBSpoiler</a> - <?php _e('плагин позволит вам спрятать текст под тегами [spoiler]текст[/spoiler].', 'russian-number-comments'); ?></li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/easy-textillate/">Easy Textillate</a> - <?php _e('плагин очень красиво анимирует текст (шорткодами в записях и виджетах или PHP-кодом в файлах темы).', 'russian-number-comments'); ?> </li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/cool-image-share/">Cool Image Share</a> - <?php _e('плагин добавляет иконки социальных сетей на каждое изображение в ваших записях.', 'russian-number-comments'); ?> </li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/today-yesterday-dates/">Today-Yesterday Dates</a> - <?php _e('относительные даты для записей за сегодня и вчера.', 'russian-number-comments'); ?> </li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/truncate-comments/">Truncate Comments</a> - <?php _e('плагин скрывает длинные комментарии js-скриптом (в стиле Яндекса или Амазона).', 'russian-number-comments'); ?> </li>
            <li><a target="new" href="https://ru.wordpress.org/plugins/easy-yandex-share/">Easy Yandex Share</a> - <?php _e('продвинутый вывод блока "Яндекс.Поделиться".', 'russian-number-comments'); ?></li>
            
            </ul>
      </div>     
    </div>
</div>
<?php wp_nonce_field( plugin_basename(__FILE__), 'rnc_nonce'); ?>
</form>
</div>
</div>
<?php 
}
//функция вывода страницы настроек плагина end

//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" begin
function rnc_menu() {
	add_options_page('Russian Number Comments', 'Russian Number Comments', 'manage_options', 'russian-number-comments.php', 'rnc_options_page');
}
add_action('admin_menu', 'rnc_menu');
//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" end


//фильтр на вывод функции comments_number с числом комментариев begin
function rnc_comments_number ($output, $number) {
    $rnc_options = get_option('rnc_options');
    $zero = stripslashes($rnc_options['zero']);
    $one = stripslashes($rnc_options['one']);
    $two = stripslashes($rnc_options['two']);
    $more = stripslashes($rnc_options['more']);
    
    if ($number != 0) {
        $output = rnc_russian_number($number, array($one, $two, $more));
    } else {
        $output = $zero;
    }    
  
    return $output;
}
add_filter('comments_number', 'rnc_comments_number', 10, 2);
//фильтр на вывод функции comments_number с числом комментариев end

//функция склонения слов после числа begin
function rnc_russian_number($number, $titles) {  
    $cases = array (2, 0, 1, 1, 1, 2);  
    return $number . ' ' . $titles[ ($number%100 > 4 && $number %100 < 20) ? 2 : $cases[min($number%10, 5)] ];  
}  
//функция склонения слов после числа end

//функция замены comments_popup_link (поддержка атрибута title) begin
function russian_comments($zero, $one, $more, $two, $titlelink = 'Комментировать статью &quot;%s&quot;', $close = 'Комментарии отключены') {
    
    $one = str_replace('% ', '', $one);
    $two = str_replace('% ', '', $two);
    $more = str_replace('% ', '', $more);
    
    $rnc_options = get_option('rnc_options');
    if (!$zero) $zero = stripslashes($rnc_options['zero']);
    if (!$one) $one = stripslashes($rnc_options['one']);
    if (!$two) $two = stripslashes($rnc_options['two']);
    if (!$more) $more = stripslashes($rnc_options['more']);

    $id = get_the_ID();
    $title = attribute_escape(get_the_title());
	$number = get_comments_number( $id );

	if ( 0 == $number && !comments_open() && !pings_open() ) {
		echo '<span' . ((!empty($css_class)) ? ' class="' . esc_attr( $css_class ) . '"' : '') . '>' . $close . '</span>';
		return;
	}

	if ( post_password_required() ) {
		_e( 'Enter your password to view comments.' );
		return;
	}

	echo '<a href="';
	if ( 0 == $number ) {
		$respond_link = get_permalink() . '#respond';
		echo apply_filters( 'respond_link', $respond_link, $id );
	} else {
		comments_link();
	}
	echo '"';

	if ( !empty( $css_class ) ) {
		echo ' class="'.$css_class.'" ';
	}

	$attributes = '';
	echo apply_filters( 'comments_popup_link_attributes', $attributes );
    
    echo ' title="' . sprintf( ($titlelink), $title ) .'"';

	echo '>';
	if ($number != 0) {
        $output = rnc_russian_number($number, array($one, $two, $more));
    } else {
        $output = $zero;
    } 
    echo $output;
	echo '</a>';
}
//функция замены comments_popup_link (поддержка атрибута title) end