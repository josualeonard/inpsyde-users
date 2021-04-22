<?php // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar
    // Set defaults.
    $args = wp_parse_args(
        $args,
        ['uri' => ''],
        ['pluginUrl' => ''],
        ['data' => []]
    );
    $uri = $args['uri'];
    $pluginUrl = $args['pluginUrl'];
    $message = esc_html(isset($args['data']['message'])?$args['data']['message']:"Unknown error");
    $user = isset($args['data']['user'])?$args['data']['user']:false;
    $name = esc_html(isset($user['name'])?$user['name']:"not exists");
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
  </head>
  <body>
    <div class="container">
      <h2 class="title">User <?=esc_html($name)?></h2>

      <!-- We will put our React component inside this div. -->
      <div id="users" class="table">
        <?php if ($user) { ?>
        <div class="row">
            <div class="cell col-label">ID</div>
            <div class="cell col-content"><?=esc_html($user['id'])?></div>
        </div>
        <div class="row">
            <div class="cell col-label">Name</div>
            <div class="cell col-content"><?=esc_html(isset($user['name'])?$user['name']:'')?></div>
        </div>
        <div class="row">
            <div class="cell col-label">Username</div>
            <div class="cell col-content"><?=esc_html(isset($user['username'])?$user['username']:'')?></div>
        </div>
        <div class="row">
            <div class="cell col-label">Email</div>
            <div class="cell col-content"><?=esc_html(isset($user['email'])?$user['email']:'')?></div>
        </div>
        <div class="row">
            <div class="cell col-label">Address</div>
            <div class="cell col-content">
                <?=esc_html(isset($user['address']['street'])?$user['address']['street']:'')?> 
                <?=esc_html(isset($user['address']['suite'])?$user['address']['suite']:'')?> 
                <?=esc_html(isset($user['address']['city'])?$user['address']['city']:'')?>
                <?=esc_html(isset($user['address']['zipcode'])?$user['address']['zipcode']:'')?>
            </div>  
        </div>
        <div class="row">
            <div class="cell col-label">Phone</div>
            <div class="cell col-content"><?=esc_html(isset($user['phone'])?$user['phone']:'')?></div>
        </div>
        <div class="row">
            <div class="cell col-label">Website</div>
            <div class="cell col-content"><?=esc_html(isset($user['website'])?$user['website']:'')?></div>
        </div>
        <div class="row">
            <div class="cell col-label">Company</div>
            <div class="cell col-content">
                <?=esc_html(isset($user['company']['name'])?$user['company']['name']:'')?>
            </div>
        </div>
        <?php } else { ?>
        <div class="row">
          <div class="cell col-content"><?=esc_html($message)?></div>
        </div>
        <?php } ?>
      </div>
      <a class="btn btn-primary" href="<?=esc_html($uri)?>">Back</a>

      <!-- Load React. -->
      <!-- Note: when deploying, replace "development.js" with "production.min.js". -->
      <script src="<?=esc_html($pluginUrl)?>js/react.development.js" crossorigin></script>
      <script src="<?=esc_html($pluginUrl)?>js/react-dom.development.js" crossorigin></script>

      <!-- Load our React component. -->
      <script src="<?=esc_html($pluginUrl)?>js/users.js"></script>

    </div>
  </body>
</html>