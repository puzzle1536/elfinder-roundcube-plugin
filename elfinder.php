<?php

/**
 * elFinder plugin for RoundCube
 *
 * This plugin integrates elFinder into Roundcube for various purpose.
 *
 * Copyright 2014 - Puzzle <puzzle1536@gmail.com>
 *
 * @version 0.1
 * @author puzzle1536@gmail.com
 * @licence GNU GPLv3+
 * @url http://github.com/puzzle1536/elfinder-roundcube-plugin.git
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 *
 *
 */

class elfinder extends rcube_plugin
{
    // all task excluding 'login' and 'logout'
    public $task = '?(?!login|logout).*';

    function init()
    {
      $rcmail = rcmail::get_instance();

      // Include elfinder js & css
      $this->include_stylesheet($this->local_skin_path() . "/css/elfinder.min.css");
      $this->include_stylesheet($this->local_skin_path() . "/css/theme.css");
      $this->include_stylesheet($this->local_skin_path() . "/elfinder.css");
      $this->include_script("js/elfinder.min.js");
      $this->include_script("elfinder.js");

      // Register hooks and actions
      $this->register_action('plugin.elfinder.save_attachments', array($this, 'save_attachments'));
      $this->register_action('plugin.elfinder.save_messages', array($this, 'save_messages'));
      $this->register_action('plugin.elfinder.load_attachments', array($this, 'load_attachments'));

      // Hack for custom skins
      if (strcmp($rcmail->config->get('skin'),"roundcube-skin-for-netbook") == 0) {
          $this->add_hook('template_object_composeattachmentlist', array($this, 'add_attachment_elfinder'));
      }

      // register actions
      $this->register_task('elfinder');
      $this->register_action('index', array($this, 'elfinder_action'));

      // Add localized strings
      $this->add_texts('localization/', array('wait_load', 'wait_save'));

      // add taskbar button
      $this->add_button(array(
          'command'    => 'elfinder',
          'class'      => 'button-elfinder',
          'classsel'   => 'button-elfinder button-selected',
          'innerclass' => 'button-inner',
          'label'      => 'elfinder.elfinder',
      ), 'taskbar');

      // add compose toolbar button
      if ($rcmail->action == 'compose') {
            $this->add_button(array(
                'name'       => 'elfinder',
                'type'       => 'link',
                'class'      => 'button button-elfinder',
                'classact'   => 'button button-elfinder button-selected',
                'label'      => 'elfinder.elfinder',
                'onclick'    => "window.parent.elfinder_load(); return false",
            ), 'toolbar');
      }

      // add button to messagemenu
      $this->add_button(array(
          'id'         => 'messagemenu_elfinder_save_msg',
          'type'       => 'link',
          'label'      => 'elfinder.save_msg',
          'name'       => 'elfinder',
          'command'    => 'elfinder-save-msg',
          'class'      => 'icon inactive',
          'classpas'   => 'icon inactive',
          'classact'   => 'icon active',
          'innerclass' => 'icon elfinder',
          'wrapper'    => 'li',
      ), 'messagemenu');

      // add button to messagemenu
      $this->add_button(array(
          'id'         => 'messagemenu_elfinder_save_all',
          'type'       => 'link',
          'label'      => 'elfinder.save_all',
          'name'       => 'elfinder',
          'command'    => 'elfinder-save-all',
          'class'      => 'icon inactive',
          'classpas'   => 'icon inactive',
          'classact'   => 'icon active',
          'innerclass' => 'icon elfinder',
          'wrapper'    => 'li',
      ), 'messagemenu');

      // add button to attachmentmenu
      $this->add_button(array(
          'id'         => 'attachmenuelfinder',
          'type'       => 'link',
          'label'      => 'elfinder.save',
          'name'       => 'elfinder',
          'command'    => 'elfinder-save',
          'class'      => 'icon active',
          'innerclass' => 'icon elfinder',
          'wrapper'    => 'li',
      ), 'attachmentmenu');

      // Load plugin's config file
      $this->load_config();

    }

    function elfinder_action()
    {
        $rcmail = rcmail::get_instance();

        // Add tinyMCE editor to 'elfinder' page
        //$rcmail->html_editor();
        $this->include_script("js/tinymce/tinymce.min.js");

        $rcmail->output->set_pagetitle('elFinder');
        $rcmail->output->send('elfinder.elfinder');
    }

    /**
     * Place a link/button after attachments listing to trigger elfinder selection
     */
    public function add_attachment_elfinder($p)
    {
        $p['content'] = "<input type=\"button\" class=\"button\" value=\"elFinder\"".
                        "onclick=\"elfinder_load();return false\">".$p['content'];

        return $p;
    }

    /**
     * Handler for message save action
     */
    public function save_messages()
    {
        $rcmail = rcmail::get_instance();
        $rcmail->output->reset();

        $files_path = $rcmail->config->get('files_path');
        $files_url  = $rcmail->config->get('files_url');

        // Convert and secure provided path
        $relpath = urldecode(get_input_value('_dirpath', RCUBE_INPUT_GET));
        $dirpath = $files_path . str_replace($files_url, "", $relpath);
        $dirpath = str_replace("..", "", $dirpath);

        $uid_array = get_input_value('_uid', RCUBE_INPUT_GET);
 
        // Check if we receive an array of msg uid or only one
        if (!is_array($uid_array)) {
            $uid_array = array ($uid_array);
        }

        if (is_dir($dirpath)) {
            foreach ($uid_array as $uid) {
                $message = new rcube_message($uid);
                // We request to save message eml
                $filename = $message->subject . '.eml';
                
                $fp = fopen($dirpath.'/'.$filename, 'w');
                $rcmail->storage->get_raw_body($message->uid, $fp);
                fclose($fp);
             
                $rcmail->output->show_message("\"".$filename."\" saved to ".$relpath, 'confirmation');
            }
        } else {
            $rcmail->output->show_message("\"$relpath\" is not a valid folder", 'error');
        }
        $rcmail->output->send('iframe');
    }

    /**
     * Handler for attachment save action
     */
    public function save_attachments()
    {
        $rcmail = rcmail::get_instance();
        $rcmail->output->reset();

        $files_path = $rcmail->config->get('files_path');
        $files_url  = $rcmail->config->get('files_url');

        // Convert and secure provided path
        $relpath = urldecode(get_input_value('_dirpath', RCUBE_INPUT_GET));
        $dirpath = $files_path . str_replace($files_url, "", $relpath);
        $dirpath = str_replace("..", "", $dirpath);

        $uid_array = get_input_value('_uid', RCUBE_INPUT_GET);
        $attach_id = get_input_value('_attachment_id', RCUBE_INPUT_GET);

        // Check if we receive an array of msg uid or only one
        if (!is_array($uid_array)) {
            $uid_array = array ($uid_array);
        }

        if (is_dir($dirpath)) {
            foreach ($uid_array as $uid) {
                // We request to save message attachments
                $message = new rcube_message($uid);

                foreach ($message->attachments as $part) {
                    $pid = $part->mime_id;
             
                    if ($attach_id && ($pid != $attach_id))
                        continue;
             
                    $part = $message->mime_parts[$pid];
                    $disp_name = $part->filename;
                    
                    if ($part->body) {
                        $fp = fopen($dirpath.'/'.$disp_name, 'w');
                        fwrite($fp, $part->body);
                        fclose($fp);
                    } else {
                        $fp = fopen($dirpath.'/'.$disp_name, 'w');
                        $rcmail->storage->get_message_part($uid, $part->mime_id, $part, null, $fp, true);
                        fclose($fp);
                    }
                    $rcmail->output->show_message("\"".$disp_name."\" saved to ".$relpath, 'confirmation');
                }
            }
        } else {
            $rcmail->output->show_message("\"$relpath\" is not a valid folder", 'error');
        }
        $rcmail->output->send('iframe');
    }

    /**
     * Handler for attachment load action
     */
    public function load_attachments()
    {
        $rcmail = rcmail::get_instance();

        $files_path = $rcmail->config->get('files_path');
        $files_url  = $rcmail->config->get('files_url');

        // Convert and secure provided path
        $filepath = urldecode(get_input_value('_filepath', RCUBE_INPUT_GET));
        $filepath = $files_path . str_replace($files_url, "", $filepath);
        $filepath = str_replace("..", "", $filepath);

        $uploadid = get_input_value('_uploadid', RCUBE_INPUT_GET);

        $COMPOSE_ID = get_input_value('_id', RCUBE_INPUT_GPC);
        $COMPOSE    = null;
        
        if ($COMPOSE_ID && $_SESSION['compose_data_' . $COMPOSE_ID]) {
          $SESSION_KEY = 'compose_data_' . $COMPOSE_ID;
          $COMPOSE =& $_SESSION[$SESSION_KEY];
        }
        
        if (!$COMPOSE) {
          die("Invalid session var!");
        }

        $rcmail->output->reset();

        if (is_file($filepath)) {

            // Copy file to temporary location
            $temp_dir = $rcmail->config->get('temp_dir');
            $tmpfname = tempnam($temp_dir, 'rcmAttmnt');
            copy($filepath, $tmpfname);

            $attachment = array(
              'path' => $tmpfname,
              'size' => filesize($filepath),
              'name' => basename($filepath),
              'mimetype' => rc_mime_content_type($filepath, basename($filepath)),
              'group' => $COMPOSE_ID,
            );

            $attachment = $rcmail->plugins->exec_hook('attachment_save', $attachment);

            $id = $attachment['id'];

            // store new attachment in session
            $rcmail->session->append($SESSION_KEY.'.attachments', $id, $attachment);
   
            if (($icon = $COMPOSE['deleteicon']) && is_file($icon)) {
              $button = html::img(array(
                'src' => $icon,
                'alt' => rcube_label('delete')
              ));
            }
            else if ($COMPOSE['textbuttons']) {
              $button = Q(rcube_label('delete'));
            }
            else {
              $button = '';
            }
   
            $content = html::a(array(
              'href' => "#delete",
              'onclick' => sprintf("return %s.command('remove-attachment','rcmfile%s', this)",
                                    JS_OBJECT_NAME, $id),
              'title' => rcube_label('delete'),
              'class' => 'delete',
            ), $button);
   
            $content .= Q($attachment['name']);
   
            $rcmail->output->command('add2attachment_list', "rcmfile$id", array(
              'html' => $content,
              'name' => $attachment['name'],
              'mimetype' => $attachment['mimetype'],
              'classname' => rcmail_filetype2classname($attachment['mimetype'], $attachment['name']),
              'complete' => true), $uploadid);
   
            // send html page with JS calls as response
            $rcmail->output->command('auto_save_start', false);
            $rcmail->output->send('iframe');

        } else {

            $rcmail->output->show_message("\"$filepath\" is not a file", 'error');
            $rcmail->output->send('iframe');

        }
    }


}
