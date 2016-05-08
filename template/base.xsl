<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
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

    <style>
      .login-form {
      width: 25rem;
      height: 18.75rem;
      position: fixed;
      top: 50%;
      margin-top: -9.375rem;
      left: 50%;
      margin-left: -12.5rem;
      background-color: #ffffff;
      opacity: 0;
      -webkit-transform: scale(.8);
      transform: scale(.8);
      }
    </style>

  </xsl:template>

  <xsl:template name="javascript">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <script type="text/javascript"
            src="https://cdn.rawgit.com/olton/Metro-UI-CSS/master/build/js/metro.min.js"></script>
  </xsl:template>

</xsl:stylesheet>