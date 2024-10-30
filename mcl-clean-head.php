<?php
/* --------------------------------------------------------------
Plugin Name: Mcl Clean head
Plugin URI: http://memocarilog.info/
Description: This Plugin is clean of head tag in your theme. ( Remove → wp_generator meta tag / rsd link tag / wlwmanifest link tag / wp_open_sans style tag / recent_comments style tag / print_emoji's script and style tag )
Text Domain: MclCleanHead
Domain Path: /languages
Version: 0.1
Author: Saori Miyazaki
Author URI: http://memocarilog.info/
License: GPL2
-------------------------------------------------------------- */
/*  
Copyright 2015 Saori Miyazaki ( email : saomocari@gmail.com )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */

/* -----------------------------------------------------------
	プラグイン有効語の設定リンク表示 
----------------------------------------------------------- */
function mcl_cleanhead_action_links( $links, $file ) {
	if ( plugin_basename( __FILE__ ) == $file ) {
		$settings_link = sprintf( '<a href="%1$s">%2$s</a>', 
		admin_url( 'options-general.php?page=mcl-clean-head.php' ), 
		__( 'Settings' , 'MclCleanHead' ) );
		array_unshift( $links, $settings_link );
	}
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mcl_cleanhead_action_links', 10, 2 );

/* -----------------------------------------------------------
	アンインストール時のオプションデータ削除 
----------------------------------------------------------- */
function mcl_clean_head_uninstall() {
	delete_option( 'mcl_head_clean_option' );
}

function mcl_clean_head_option_init() {
	register_uninstall_hook( __FILE__, 'mcl_clean_head_uninstall' );
}
add_action( 'admin_init', 'mcl_clean_head_option_init' );

/* -----------------------------------------------------------
	テキストドメイン読み込み 
----------------------------------------------------------- */
function mcl_clean_head_textdomain() {
	load_plugin_textdomain( 'MclCleanHead', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'mcl_clean_head_textdomain' );

/* load_plugin_textdomain('MclCleanHead', false, basename( dirname( __FILE__ ) ) . '/languages' ); */

/* -----------------------------------------------------------
	管理画面メニューへメニュー項目を追加
----------------------------------------------------------- */
add_action( 'admin_menu', 'mcl_add_admin_menu' );
function mcl_add_admin_menu() {
	add_options_page(
		__( 'Cleanhead Setting', 'MclCleanHead' ),
		__( 'Cleanhead Setting', 'MclCleanHead' ),
		'manage_options',
		'mcl-clean-head.php',
		'mcl_clean_head_admin' // 定義した関数を呼び出し
	);
}

/* -----------------------------------------------------------
	管理画面 CSS ファイル読み込み 
----------------------------------------------------------- */
function mcl_admin_style($hook) {
    if ( 'settings_page_mcl-clean-head' != $hook ) {
        return;
    }
    wp_enqueue_style( 'mcl_head_clean_style', plugin_dir_url( __FILE__ ) . 'css/mcl-admin-style.css' );
}
add_action( 'admin_enqueue_scripts', 'mcl_admin_style' );

/* -----------------------------------------------------------
	管理画面を作成する関数を定義
----------------------------------------------------------- */
function mcl_clean_head_admin(){ ?>
	
	<div class="wrap">
	<h2><?php _e( 'Mcl Cleanhead Setting', 'MclCleanHead' ); ?></h2>
	<p><?php _e( 'Please save the settings and put a check on the tag you want to Output.', 'MclCleanHead' ); ?><!-- デフォルト時は以下のタグが HTML head 内より除去されています。<br />表示したいタグにはチェックを入れて設定を保存してください。 --></p>
	
	<div class="postbox">
	
	<form id="mcl_clean_head_form" method="post" action="">
	<?php // nonce を発行
		wp_nonce_field( 'mcl_head_clean_options', 'mcl_head_clean_nonce' ); 
		$options = get_option( 'mcl_head_clean_option' );	
		
		// チェックボックスを定義
		function mcl_clean_head_checkbox( $options, $label, $name ){ ?>
			<p class="checkbox">
				<input id="<?php echo esc_attr( $name ); ?>" type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="1" <?php echo !empty( $options[$name] ) ? 'checked': '' ; ?> />
				<label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_attr( $label ); ?></label>
			</p>
		<?php 
		} ?>
				
		<table class="mcl_clean_head_table inside">
			<tr>
				<th><?php _e( 'Output meta tag generator', 'MclCleanHead' ); ?></th>
				<td>
				<?php
					$label = __( 'Output meta tag generator', 'MclCleanHead' );
					$name = 'mcl_hc_generator';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Output link tag for EditURI', 'MclCleanHead' ); ?></th>
				<td>
				<?php
					$label = __( 'Output link tag for EditURI application/rsd+xml', 'MclCleanHead' );
					$name = 'mcl_hc_rsdxml';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Output link tag for wlwmanifest', 'MclCleanHead' ); ?></th>
				<td>
				<?php
					$label = __( 'Output link tag for wlwmanifest application/wlwmanifest+xml', 'MclCleanHead' );;
					$name = 'mcl_hc_wlwmanifest';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Output link tag stylesheet for open-sans', 'MclCleanHead' ); ?></th>
				<td>
				<?php
					$label = __( 'Output link tag stylesheet for open-sans', 'MclCleanHead' );
					$name = 'mcl_hc_opensans';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Output style tag for recentcomments', 'MclCleanHead' ); ?></th>
				<td>
				<?php
					$label = __( 'Output style tag for recentcomments', 'MclCleanHead' );
					$name = 'mcl_hc_comments_style';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
			<tr>
				<th><?php _e( 'Output style tag and script for emoji', 'MclCleanHead' ); ?></th>
				<td>
				<?php
					$label = __( 'Output style tag and script for emoji', 'MclCleanHead' );
					$name = 'mcl_hc_print_emoji';
					mcl_clean_head_checkbox( $options, $label, $name);
				?>	
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
	</div>
	</div>
<?php 
} // mcl_clean_head_admin

/* -----------------------------------------------------------
	フォームの値を受け取りデータベースへ保存する
----------------------------------------------------------- */
add_action( 'admin_init', 'mcl_head_clean_update');
function mcl_head_clean_update(){
	if( isset( $_POST['mcl_head_clean_nonce'] ) && $_POST['mcl_head_clean_nonce'] ){
		// nonce のチェック
		if( check_admin_referer( 'mcl_head_clean_options', 'mcl_head_clean_nonce' ) ){
			
			// 4. フォーム値の保存処理
			$mcl_hc_generator = isset( $_POST['mcl_hc_generator'] ) ? intval( $_POST['mcl_hc_generator'] ) : '';
			$mcl_hc_rsdxml = isset( $_POST['mcl_hc_rsdxml'] ) ? intval( $_POST['mcl_hc_rsdxml'] ) : '';
			$mcl_hc_wlwmanifest = isset( $_POST['mcl_hc_wlwmanifest'] ) ? intval( $_POST['mcl_hc_wlwmanifest'] ) : '';
			$mcl_hc_opensans = isset( $_POST['mcl_hc_opensans'] ) ? intval( $_POST['mcl_hc_opensans'] ) : '';
			$mcl_hc_comments_style = isset( $_POST['mcl_hc_comments_style'] ) ? intval( $_POST['mcl_hc_comments_style'] ) : '';
			$mcl_hc_print_emoji = isset( $_POST['mcl_hc_print_emoji'] ) ? intval( $_POST['mcl_hc_print_emoji'] ) : '';
			
			$array_options = array( 
				'mcl_hc_generator' => $mcl_hc_generator,
				'mcl_hc_rsdxml' => $mcl_hc_rsdxml,
				'mcl_hc_wlwmanifest' => $mcl_hc_wlwmanifest,
				'mcl_hc_rsdxml' => $mcl_hc_rsdxml,
				'mcl_hc_opensans' => $mcl_hc_opensans,
				'mcl_hc_comments_style' => $mcl_hc_comments_style,
				'mcl_hc_print_emoji' => $mcl_hc_print_emoji
			);
			update_option( 'mcl_head_clean_option', $array_options );
			
			add_action('admin_notices', 'my_admin_notice');	
			// リダイレクトして再度フォームが送信されるエラーを防ぐ
			wp_safe_redirect( menu_page_url( 'mcl-clean-head', false ) );
		}
	}
}

/* -----------------------------------------------------------
	保存しましたのメッセージ
----------------------------------------------------------- */
function my_admin_notice() { ?>
    <div class="updated">
        <p><?php _e( 'Updated!', 'MclCleanHead' ); ?></p>
    </div>
<?php
}

/* -----------------------------------------------------------
	フック処理
----------------------------------------------------------- */
$options = get_option( 'mcl_head_clean_option' );

// generator 標記の削除
if( empty( $options['mcl_hc_generator'] ) ){
	remove_action( 'wp_head', 'wp_generator' );
}

// RSD(RPC用XML) application/rsd+xml を削除
if( empty( $options['mcl_hc_rsdxml'] ) ){
	remove_action( 'wp_head', 'rsd_link' );
}

// wlwmanifest.xml の読み込み削除（ブログ編集ツール）
if( empty( $options['mcl_hc_wlwmanifest'] ) ){
	remove_action( 'wp_head', 'wlwmanifest_link' );
}

// Open sans フォントの読み込み削除
if( empty( $options['mcl_hc_opensans'] ) ){
    function remove_wp_open_sans() {
        wp_deregister_style( 'open-sans' );
        wp_register_style( 'open-sans', false );
    }
    add_action( 'wp_enqueue_scripts', 'remove_wp_open_sans');
}

// 「最近のコメント」ウィジェット用のスタイルを削除
if( empty( $options['mcl_hc_comments_style'] ) ){
	function remove_recent_comments_style() {
		global $wp_widget_factory;
		remove_action( 'wp_head', array( $wp_widget_factory -> widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
	}
	add_action( 'widgets_init', 'remove_recent_comments_style' );
}

// 絵文字のスタイルとスクリプトの読み込み削除
if( empty( $options['mcl_hc_print_emoji'] ) ){
	remove_action( 'wp_head', 'print_emoji_detection_script' , 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );	
}