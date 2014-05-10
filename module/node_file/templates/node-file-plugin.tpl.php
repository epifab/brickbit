<!DOCTYPE HTML>
<html lang="<?php echo $system['lang']; ?>">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?php echo $this->api->Path('favicon.ico'); ?>">
    <meta name="description" content="<?php echo isset($page['description']) ? $page['description'] : ''; ?>">
    <meta name="author" content="<?php echo isset($page['author']) ? $page['author'] : ''; ?>">
    <title><?php echo $page['title']; ?></title>

    <?php foreach ($page['css'] as $css): ?>
    <link href="<?php echo $css; ?>" type="text/css" rel="stylesheet"/>
    <?php endforeach; ?>

    <?php foreach ($page['js']['script'] as $src): ?>
    <script type="text/javascript" src="<?php echo $src; ?>"></script>
    <?php endforeach; ?>
<!--[if lte IE 9]>
  <script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.3.0/respond.js"></script>
<![endif]-->
    <script type="text/javascript">
    <?php foreach ($page['js']['data'] as $key => $jsonData): ?>
    brickbit.data("<?php echo $key; ?>", <?php echo $jsonData; ?>);
    <?php endforeach; ?>
    </script>
  </head>

  <body class="<?php echo (isset($page['bodyClass']) ? $page['bodyClass'] : ''); ?>">
    <?php foreach ($page['css'] as $css): ?>
    <link href="<?php echo $css; ?>" type="text/css" rel="stylesheet"/>
    <?php endforeach; ?>

    <?php foreach ($page['js']['script'] as $src): ?>
    <script type="text/javascript" src="<?php echo $src; ?>"></script>
    <?php endforeach; ?>

    <script type="text/javascript">
    <?php foreach ($page['js']['data'] as $key => $jsonData): ?>
    brickbit.data("<?php echo $key; ?>", <?php echo $jsonData; ?>);
    <?php endforeach; ?>
    </script>

    <?php foreach ($node->files as $nodeIndex => $files): ?>
      <h3><?php echo $nodeIndex; ?></h3>
      <table class="table table-striped">
        <?php foreach ($files as $file): ?>
            <?php if ($file->image): ?>
              <tr>
                <td>
                  <img src="<?php echo $file->image_urls['thumb']; ?>" />
                </td>
                <td>
                  <?php foreach ($file->image_urls as $version => $urn): ?>
                  <a href="#<?php echo $version; ?>" data-imgurl="<?php echo $this->api->url($urn); ?>" class="btn btn-primary nodefile-image"><?php echo $version; ?></a>
                  <?php endforeach; ?>
                </td>
              </tr>
            <?php endif; ?>
        <?php endforeach; ?>
      </table>
    <?php endforeach; ?>

    <script type="text/javascript">
      $('a.nodefile-image').click(function() {
        top.tinymce.activeEditor.insertContent('<img src="' + $(this).data('imgurl') + '" />');
        top.tinymce.activeEditor.windowManager.close();
      });
    </script>
  </body>
</html>