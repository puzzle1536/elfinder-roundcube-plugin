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

//require_once('plugins/filesystem_attachments/filesystem_attachments.php');

//class elfinder extends filesystem_attachments
class elfinder extends rcube_plugin
{
  public $task = 'mail';

  function init()
  {
      $rcmail = rcmail::get_instance();

      // Include elfinder js & css
      $this->include_stylesheet($this->local_skin_path() . "/css/elfinder.min.css");
      $this->include_stylesheet($this->local_skin_path() . "/css/theme.css");
      $this->include_script("js/elfinder.min.js");
      $this->include_script("elfinder.js");

      // Register hooks and actions
//      $this->add_hook('template_object_messageattachments', array($this, 'attachment_elfinder'));
      $this->add_hook('template_object_composeattachmentlist', array($this, 'attachment_elfinder'));
      $this->register_action('plugin.elfinder.elfinder_attachments', array($this, 'save_attachements'));

  }

    /**
     * Place a link/button after attachments listing to trigger download
     */
    public function attachment_elfinder($p)
    {
        $rcmail = rcmail::get_instance();

        $link = html::a(array(
            'value' => 'briefcase',
            'type' => 'button',
            'class' => 'button',
            'onclick' => 'load_briefcase();',
            'style' => 'text-align:center',
            ),
            Q('briefcase')
        );

        $p['content'] .= $link;

        return $p;
    }

    /**
     * Handler for attachment download action
     */
    public function save_attachements()
    {
        $rcmail = rcmail::get_instance();
        $filepath = get_input_value('_filepath', RCUBE_INPUT_GET);
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
            $attachment = array(
              'path' => $filepath,
              'size' => filesize($filepath),
              'name' => basename($filepath),
              'mimetype' => rc_mime_content_type($filepath, basename($filepath)),
              'group' => $COMPOSE_ID,
            );

//            $attachment = $rcmail->plugins->exec_hook('attachment_save', $attachment);

        $group = $args['group'];

        $userid = rcmail::get_instance()->user->ID;
        list($usec, $sec) = explode(' ', microtime());
        $attachment['id'] = preg_replace('/[^0-9]/', '', $userid . $sec . $usec);
        $attachment['status'] = true;


            $id = $attachment['id'];

            // store new attachment in session
            unset($attachment['status'], $attachment['abort']);
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

        }

    }


}
