<?php // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar
    // Set defaults.
    $args = wp_parse_args(
        $args,
        ['uri' => ''],
        ['url' => ''],
        ['pluginUrl' => ''],
        ['data' => []]
    );
    $uri = $args['uri'];
    $url = $args['url'];
    $pluginUrl = $args['pluginUrl'];
    $data = isset($args['data'])?$args['data']:[];
    $code = esc_html(isset($data['code'])?$data['code']:0);
    $message = esc_html(isset($data['message'])?$data['message']:"Unknown error");
    $users = isset($data['users'])?$data['users']:[];
    $sep = (get_option('permalink_structure')==="")?$uri.'&':'?';
    ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Users Table</title>
    <link rel="shortcut icon" type="image/jpg" href="<?=esc_html($pluginUrl)?>images/logo.png"/>
    <link rel="stylesheet" 
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        crossorigin>
    <link rel="stylesheet" href="<?=esc_html($pluginUrl)?>css/style.css" crossorigin>
    <script type="text/javascript">
        let users = <?=json_encode($users);?>;
        let code = <?=esc_html($code)?>;
        let message = "<?=esc_html($message)?>";
        let uri = "<?=esc_html($uri)?>";
        let url = "<?=esc_html($url)?>";
        let sep = "<?=esc_html($sep)?>";
    </script>
  </head>
  <body>
    <div class="container">
      <h2 class="title">Users</h2>
      <p>This page demonstrates showing users table on initial load, and using React for the frontend
      with no build tooling.</p>

      <!-- We will put our React component inside this div. -->
      <!-- Keep filling this element, just in case javascript is not working -->
      
      <div id="users">
        <div class="table">
          <?php if (count($users)>0) { ?>
          <div class="row head">
            <div class="cell user-id">ID</div>
            <div class="cell user-name">Name</div>
            <div class="cell user-username">Username</div>
            <div class="cell user-email">Email</div>
          </div>
                <?php
                for ($i=0; $i<count($users); $i++) {
                    $user = $users[$i];
                    $userUrl = esc_html($sep.'id='.$user['id']);
                    ?>
          <div id="<?=esc_html($user['id'])?>" data-id=<?=esc_html($user['id'])?> class="row">
            <div class="cell user-id">
              <a href="<?=esc_html($userUrl)?>"><?=esc_html($user['id'])?></a>
            </div>
            <div class="cell user-name">
              <a href="<?=esc_html($userUrl)?>"><?=esc_html($user['name'])?></a>
            </div>
            <div class="cell user-username">
              <a href="<?=esc_html($userUrl)?>"><?=esc_html($user['username'])?></a>
            </div>
            <div class="cell user-email">
              <a href="<?=esc_html($userUrl)?>"><?=esc_html($user['email'])?></a>
            </div>
          </div>
                <?php }
            } else { ?>
          <div class="row">
            <div class="cell"><?=($code!==200)?esc_html($message):'Nothing to show'?></div>
          </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <!-- Load React. -->
      <!-- Note: when deploying, replace "development.js" with "production.min.js". -->
      <!-- script src="<?=esc_html($pluginUrl)?>js/react.development.js" crossorigin></script -->
      <!-- script src="<?=esc_html($pluginUrl)?>js/react-dom.development.js" crossorigin></script -->
      <script src="<?=esc_html($pluginUrl)?>js/react.production.min.js" crossorigin></script>
      <script src="<?=esc_html($pluginUrl)?>js/react-dom.production.min.js" crossorigin></script>

      <!-- Load our React component. -->
      <script src="<?=esc_html($pluginUrl)?>js/users.js"></script>
    </div>
  </body>
</html>