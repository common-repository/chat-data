<?php

/**
 * Plugin Name: Chat Data
 * Plugin URI: https://www.chat-data.com/
 * Description: Embed your Chat Data chatbot on any Wordpress site.
 * Version: 1.0.0
 * Author: Chat Data
 * Author URI: https://www.chat-data.com//
 * License: GPL2
 */

add_action('admin_menu', 'chat_data_add_options_page');

// Add the options page to the admin menu
function chat_data_add_options_page()
{
    add_options_page('Chat Data Settings', 'Chat Data Options', 'administrator', 'chat_data_settings', 'chat_data_options_page');
    add_action('admin_init', 'chat_data_register_options');
}

// Register the options settings
function chat_data_register_options()
{
    register_setting('chat_data_options', 'chatbot_id');
    register_setting('chat_data_options', 'embedding_domain');
}

add_action('admin_enqueue_scripts', 'chat_data_enqueue_styles');
function chat_data_enqueue_styles($hook)
{
    if ($hook != 'settings_page_chat_data_settings') {
        return;
    }

    $css = "
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f8f9fa;
        }

        #form-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            /*height: 100vh;*/
        }

        .logo-container a {
            text-decoration: none;
            color: #000;
        }

        .form-group {
            margin-bottom: 0.5rem;
        }

        .note-label {
            font-weight: 600;
        }

        label.text-secondary {
            color: #6c757d;
        }

        input.form-control {
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .submit-btn-container {
            display: flex;
            justify-content: flex-end;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
    ";

    wp_register_style('chat_data_admin_styles', false, array(), '1.0.0');
    wp_enqueue_style('chat_data_admin_styles');
    wp_add_inline_style('chat_data_admin_styles', $css);
}
// Define the content of the options page
function chat_data_options_page()
{
    ?>
    <div class="wrap">
        <h1>
            <?php echo esc_html(get_admin_page_title()); ?>
        </h1>
        <div id="form-container">
            <form method="post" action="options.php">
                <?php settings_fields('chat_data_options'); ?>
                <?php do_settings_sections('chat_data_options'); ?>

                <div class="logo-container">
                    <a href="https://www.chat-data.com/" target="_blank">
                        <img alt="Chat Data" loading="lazy" width="64" style="color:transparent"
                            src="https://www.chat-data.com/images/chatdata_logo.svg">
                    </a>
                </div>

                <div class="form-group">
                    <label for="chatbot_id" class="text-secondary">Chatbot ID</label>
                    <input type="text" class="form-control" placeholder="Chatbot ID" name="chatbot_id" id="chatbot_id"
                        value="<?php echo esc_attr(get_option('chatbot_id')); ?>" required />
                </div>
                <div class="note-label">*Note: Copy your Chatbot ID from <a href="https://www.chat-data.com/"
                        target="_blank">Chat Data</a>
                    chatbot Settings
                    tab.</div>
                <div class="note-label">Follow this  <a href="https://cookbook.chat-data.com/docs/embed-chatbots-on-websites#allowlist-your-domain"
                        target="_blank">guide</a> to allowlist your domain.</div>

                <div class="form-group">
                    <label for="embedding_domain" class="text-secondary">Embedding Domain</label>
                    <input type="text" class="form-control" placeholder="embedding domain" name="embedding_domain" id="embedding_domain"
                    value="<?php echo esc_attr(get_option('embedding_domain', 'www.chat-data.com')); ?>" required />
                </div>
                <div class="note-label">*Note: Update this only if you are on our <a href="https://cookbook.chat-data.com/docs/white-label-your-chatbot"
                        target="_blank">Reseller Plan</a>.</div>
                <div class="submit-btn-container">
                    <?php submit_button(); ?>
                </div>
            </form>
        </div>
    </div>
    <?php
}

// Embed the script on the site using the ID entered in the options page
function chat_data_embed_chatbot()
{
    $chatbot_id = get_option('chatbot_id');
    $embedding_domain = get_option('embedding_domain');

    if ($chatbot_id && $embedding_domain) {
        $script_url = "https://{$embedding_domain}/embed.min.js?chatbotId={$chatbot_id}";
        // Register the script
        wp_register_script(
            'chatbot-script',
            $script_url,
            array(), // Dependencies (if any)
            '1.0.0', // Version number (replace with your actual script version)
            array( 
                'strategy'  => 'defer',
                'in_footer' => true,
            )
        );
        wp_enqueue_script('chatbot-script');
    }
}

add_action('wp_enqueue_scripts', 'chat_data_embed_chatbot');
?>