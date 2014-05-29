<?php

/**
 * elFinder plugin for RC
 *
 * plugin that includes elFinder into Roundcube for various purpose
 *
 * @version 0.1
 * @author Puzzle
 * @licence GNU GPL
 * @url http://github.com/puzzle1536/elfinder-roundcube-plugin
 *
 */

class elfinder extends rcube_plugin
{
//    public $task = 'mail';
    public $task = '?(?!login|logout).*';

    function init()
    {
      // Include elfinder js & css
      $this->include_stylesheet($this->local_skin_path() . "/css/elfinder.full.css");
      $this->include_stylesheet($this->local_skin_path() . "/css/theme.css");
      $this->include_stylesheet($this->local_skin_path() . "/elfinder.css");
      $this->include_script("js/elfinder.min.js");
      $this->include_script("elfinder.js");

      // Register hooks and actions
      $this->register_action('plugin.elfinder.save_attachments', array($this, 'save_attachements'));
      $this->register_action('plugin.elfinder.load_attachments', array($this, 'load_attachments'));

      $this->add_hook('template_object_composeattachmentlist', array($this, 'add_attachment_elfinder'));

      // register actions
      $this->register_task('elfinder');
      $this->register_action('index', array($this, 'action'));
      $this->add_texts('localization/', false);

      // add taskbar button
      $this->add_button(array(
          'command'    => 'elfinder',
          'class'      => 'button-elfinder',
          'classsel'   => 'button-elfinder button-selected',
          'innerclass' => 'button-inner',
          'label'      => 'elfinder.elfinder',
      ), 'taskbar');

      // add button to messagemenu
      $this->add_button(array(
          'id'         => 'messagemenubriefcase',
          'type'       => 'link',
          'label'      => 'elfinder.save_all',
          'name'       => 'elfinder',
          'command'    => 'briefcase-save-all',
          'class'      => 'icon inactive',
          'classpas'   => 'icon inactive',
          'classact'   => 'icon active',
          'innerclass' => 'icon briefcase',
          'wrapper'    => 'li',
      ), 'messagemenu');

      // add button to attachmentmenu
      $this->add_button(array(
          'id'         => 'attachmenubriefcase',
          'type'       => 'link',
          'label'      => 'elfinder.briefcase',
          'name'       => 'elfinder',
          'command'    => 'briefcase-save',
          'class'      => 'icon active',
          'innerclass' => 'icon briefcase',
          'wrapper'    => 'li',
      ), 'attachmentmenu');

      // Load plugin's config file
      $this->load_config();

    }

    function action()
    {
        $rcmail = rcmail::get_instance();

        $rcmail->output->set_pagetitle('File Manager');

        $rcmail->output->send('elfinder.elfinder');
    }

    /**
     * Place a link/button after attachments listing to trigger elfinder selection
     */
    public function add_attachment_elfinder($p)
    {
        $p['content'] = "<input type=\"button\" class=\"button\" value=\"Briefcase\"".
                        "onclick=\"briefcase_load();return false\">".$p['content'];

        return $p;
    }

    /**
     * Handler for attachment save action
     */
    public function save_attachements()
    {
        $rcmail = rcmail::get_instance();
        $rcmail->output->reset();

        $files_path = $rcmail->config->get('files_path');
        $files_url  = $rcmail->config->get('files_url');

        // Convert and secure provided path
        $relpath = urldecode(get_input_value('_dirpath', RCUBE_INPUT_GET));
        $dirpath = $files_path . str_replace($files_url, "", $relpath);
        $dirpath = str_replace("..", "", $dirpath);

        $uid     = get_input_value('_uid', RCUBE_INPUT_GET);
        $attach_id = get_input_value('_attachment_id', RCUBE_INPUT_GET);
        $message = new rcube_message($uid);
        $imap = $rcmail->storage;

        if (is_dir($dirpath)) {
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
                    $imap->get_message_part($message->uid, $part->mime_id, $part, null, $fp, true);
                    fclose($fp);
                }
                $rcmail->output->show_message("\"".$disp_name."\" saved to ".$relpath, 'confirmation');
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
