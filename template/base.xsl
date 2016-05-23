<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="menu.xsl"/>


  <xsl:output method="html"/>
  <xsl:template name="head">
    <head>
      <xsl:call-template name="stylesheet"/>
      <xsl:call-template name="javascript"/>
    </head>
  </xsl:template>


  <xsl:template name="stylesheet">

    <link rel="stylesheet" href="https://cdn.rawgit.com/olton/Metro-UI-CSS/master/build/css/metro.min.css"/>
    <link rel="stylesheet" href="https://cdn.rawgit.com/olton/Metro-UI-CSS/master/build/css/metro-responsive.min.css"/>
    <link rel="stylesheet" href="https://cdn.rawgit.com/olton/Metro-UI-CSS/master/build/css/metro-schemes.min.css"/>
    <link rel="stylesheet" href="https://cdn.rawgit.com/olton/Metro-UI-CSS/master/build/css/metro-rtl.min.css"/>
    <link rel="stylesheet" href="https://cdn.rawgit.com/olton/Metro-UI-CSS/master/build/css/metro-icons.min.css"/>
    <link rel="stylesheet" href="{/data/system/baseUrl}css/style.css"/>


  </xsl:template>

  <xsl:template name="javascript">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.rawgit.com/olton/Metro-UI-CSS/master/build/js/metro.min.js"></script>

    <script src="{/data/system/baseUrl}js/vendor/wysihtml5-0.3.0.js"></script>
    <script src="{/data/system/baseUrl}js/vendor/advanced.js"></script>
    <script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.fileupload.js"></script>
    <script src="{/data/system/baseUrl}js/custom.js"></script>

    <script type="text/javascript">
      var baseUrl = '<xsl:value-of select="/data/system/baseUrl"/>';
      var path = '<xsl:value-of select="/data/system/requestUri"/>';
      var pagePath = '<xsl:value-of select="/data/page/path"/>';
    </script>

  </xsl:template>

</xsl:stylesheet>