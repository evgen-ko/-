<?php
/**
 * @package Download photo dphl_link
 * @version 2.3.1
 */
/*
Plugin Name: Скачать фото по ссылке
Plugin URI: https://plastilin-st.ru
Description: Поиск по номеру заказа и переход на файлообменник для скачивания исходников заказа. Номер заказа авляется АРТИКУЛОМ Для вавода необходимо добавить шорткод  [download_photo] в нужном месте. Добавлено выбор цвета кнопки поиска. Вибор целевой ссылки по результатам поиска.
Author: Evgen
Version: 2.3.2

*/

# Выход при прямом доступе
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
  
  
  /*  версия 2,0,0  */
  add_action( 'admin_menu', 'dphl_options_page' );
  add_action( 'admin_init', 'dphl_setting' ); 
  add_action( 'admin_enqueue_scripts', 'dphl_enqueue_scripts');
  
    function dphl_options_page(){
	  // $page_title, $menu_title, $capability, $menu_slug, $function
	  add_menu_page( 'Скачать фото по ссылке', 'Скачать фото', 'manage_options', 'dphl_options', 'dphl_option_page', plugins_url( 'download.png', __FILE__ ),'5,2' );
	  }
  
  function dphl_setting (){
	  
	  // $option_group, $option_name, $sanitize_callback
	register_setting( 'dphl_color_options_group', 'dphl_options_color' );
	register_setting( 'dphl_color_options_group', 'dphl_options_link_url' );

	// $id, $title, $callback, $page
	add_settings_section( 'dphl_options_section_1', 'Цвет кнопки', 'dphl_color_options_deskription', 'dphl_options' );
	add_settings_section( 'dphl_options_section_2', 'Выбрать целевую ссылку', 'dphl_link_options_deskription', 'dphl_options' );

	// $id, $title, $callback, $page, $section, $args
	add_settings_field( 'dphl_color_id', 'Какой цвет нравится??', 'dphl_color_button_callback', 'dphl_options', 'dphl_options_section_1', array( 'label_for' => 'dphl_color_id' ) );
	add_settings_field( 'dphl_link_id', 'Куда отправим клиента??', 'dphl_link_url_callback', 'dphl_options', 'dphl_options_section_2', array( 'label_for' => 'dphl_link_id' ) );
	
  } 
  // Функция add_menu_page
    function dphl_option_page(){
		
	global $select_options; if ( ! isset( $_REQUEST['settings-updated'] ) ) $_REQUEST['settings-updated'] = false;
?>
        <div class="wrap">
            <h2>Скачать фото по номеру заказа</h2>
			<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
                      <div id="message" class="updated">
                           <p><strong>Настройки сохранены</strong></p>
                      </div>
            <?php endif; ?>
			<form method="post" action="options.php">
	<?php
	      settings_fields( 'dphl_color_options_group' ); 
	      do_settings_sections('dphl_options');
	      submit_button();
    ?>
			</form>
		</div>
	<?php
	}
	
  // Колбек функция колорпиккера
   function dphl_color_button_callback(){
	 $options = get_option( 'dphl_options_color' );
  ?>
 <p>
	<input class="iris_color" name="dphl_options_color[dphl_link_color]" id="dphl_color_id" type="text" value="<?php echo $options['dphl_link_color']; ?>">
 </p>
 
 <?php }
 
  // Колбек функция выбора целевой ссылки
  function dphl_link_url_callback(){
	 $options = get_option( 'dphl_options_link_url' );
  ?>
 <p>	
    <input type="radio" name="dphl_options_link_url[dphl_link_url]" id="dphl_link" value="1" <?php checked ( 1, $options['dphl_link_url'], true) ; ?> ><label for="dphl_link"> Переход на страницу с заказом</label><br/>
    <input type="radio" name="dphl_options_link_url[dphl_link_url]" id="dphl_product_url" value="2" <?php checked( 2, $options['dphl_link_url'], true ); ?>/><label for="dphl_product_url">Переход по ссылке на файлообменник</label><br/>
 </p>
 <?php // echo 'Тест:' . $options['dphl_link_url']; 
 
 }
 
// Колбек функция add_settings_section Описание настроек секци "Цвета кнопки"
 function dphl_color_options_deskription() {
	 echo '<p>Выбираем цвет кнопки "Отправить" в форме поиска заказа</p>';
 }
 
	 
 // Колбек функция add_settings_section Описание настроек секци "Выбора целевой ссылки"
 function dphl_link_options_deskription() {
	 echo '<p>Здесь выбираем вариант куда должен попасть клиент после определения заказа</p>';
 }
  
  /**  Добавляем колор пикер
	 * Подгружаем стили и скрипты
	 */
	function dphl_enqueue_scripts($hook) {
		// Убедимся, что это страница настроек нашего плагина
		//if( 'dphl_options_color_'.'dphl_options' != $hook )
			 if($hook == 'dphl_options')
			return;
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		// подключаем свой стиль
		// wp_enqueue_script( 'script_123', plugins_url( 'script.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), false, 1 );
		add_action( 'admin_footer', 'admin_footer_script', 99 );
	}

	/**
	 * Подключаем свой скрпит в подвал
	 */
	function admin_footer_script(){
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			$('.iris_color').wpColorPicker({
			//	mode: 'hsl',
	// устанавливает цвет по умолчанию, также цвет по умолчанию
	// из атрибута value у input
	defaultColor: false,
	// функция обратного вызова, срабатывающая каждый раз 
	//при выборе цвета (когда водите мышкой по палитре)
	change: function(event, ui){ },
	// функция обратного вызова, срабатывающая при очистке (сбросе) цвета
	clear: function(){ },
	// спрятать ли выбор цвета при загрузке
	// палитра будет появляться при клике
	hide: true,
	// показывать ли группу стандартных цветов внизу палитры
	// можно добавить свои цвета указав их в массиве: ['#125', '#459', '#78b', '#ab0', '#de3', '#f0f']
	palettes: true
			});
		});
		</script>
		<?php
	}
  /*  версия 2,0,0  */
  
  
/*  версия 1,0,0  */
   function my_download_photo_code(){
   // add_action( 'wp_footer', 'download_photo_style_scripts' );
 $options = get_option( 'dphl_options_color' );
 $color_bt = $options['dphl_link_color'];
 $hiddfiled = wp_nonce_field('photo_order_number_my_action','photo_order_number_nonce_field'); // защитное скрытое поле
 $returnform = '  <form method="post" value="" class="dphl_form" >
				<div class="download_form_input">
				<div class="numberblock_input">
				<input type = "txt" name="photo_order_number" placeholder="Введите номер заказа " data-tilda-req="1" data-tilda-rule="none" class="bl_input" style="color:#000000; border:1px solid #000000; padding: 10px; " required>
				</div>' . $hiddfiled . '
				<div class="numberblock_submit">
				<input type="submit" value="Отправить" class="bl-submit" style="color:#ffffff; background-color:' . $color_bt .'"> 
				</div>
				</div>
				</form>
				<script>
				   // Обнуляум сессию, защита от повторной отправки формы
					if ( window.history.replaceState ) {
						window.history.replaceState( null, null, window.location.href );
					}
				</script>'
				;   
   
 
   
   
global $dphl_link;
 $sku = isset( $_POST['photo_order_number']) ? $_POST['photo_order_number'] : ''; // Получаем Артикул методом POST из поля ввода
$product_id = wc_get_product_id_by_sku( $sku ); // Получаем ID по Артиклу
$dphl_link = get_permalink( $product_id );           // Получаем ссылку по ID


if (isset ($_POST['photo_order_number'])) {
	
	// Проверяем скрытые поля
	if ( empty($_POST) || ! wp_verify_nonce( $_POST['photo_order_number_nonce_field'], 'photo_order_number_my_action') ){
   print 'Извините, проверочные данные не соответствуют.';
   exit;
}
else {
   if($product_id > 0){ // если существует ID товара

	$product = wc_get_product($product_id);
    $dphl_product_url = $product -> get_product_url();  // Получаем URL внешнего товара
    $options = get_option( 'dphl_options_link_url' ); // Получаем значение радиокнопок
    $dphl_radio_value = $options['dphl_link_url'];
  if( $dphl_radio_value == 1 ) { 
    $link = $dphl_link;
 } 
  if( $dphl_radio_value == 2 ) { 
    $link = $dphl_product_url;
 }
   
	echo  '<meta http-equiv="refresh" content="0; url= ' .$link. '">' ; // Редирект на страницу товара
 
	// print_r ('ТЕСТ: ' . $options['dphl_link_url']);
  // echo 'Тест:' . $dphl_radio_value  ; 
	
 }
   if(! $product_id && $sku > 0 ) { // Если отсутствует ID или Артикул товара выводим сообщение
	wc_print_notice( 
                sprintf(  'Заказа с номером "' .$sku. '"  не существует!'), 'error' ) ; 
   } else { echo '';}
   }  
}
// Подключаем стили только на странице прописанного шорткода
 add_action( 'wp_footer', 'download_photo_style_scripts' );
 
 
 return $returnform;
 
  }
  
   // Проверяем установлен ли Woocommerce.
if ( in_array(
	'woocommerce/woocommerce.php',
	apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
	true
) ) {
	// Если установлен Woocommerce добавляем шорткод  [download_photo]
	add_shortcode( 'download_photo', 'my_download_photo_code');
} else {
	add_action( 'admin_notices', 'dfl_not_woocommerce_notice' );
	return false;
}
    // Если не установлен Woocommerce выводим сообщение об ошибке.
function dfl_not_woocommerce_notice(){
	$dfl_message = "Для работы плагина <b>\"Скачать фото по ссылке\" </b> обязательным условием является установка и активация Woocommerce ";
	echo '<div class="notice notice-error is-dismissible"> <p>'. $dfl_message .'</p></div>';
}

// Подключаем стили
function download_photo_style_scripts () {
    wp_register_style( 'my_style', plugins_url('css/download-photo-style.css',__FILE__ ));
    wp_enqueue_style( 'my_style' );
}
   
   ?>