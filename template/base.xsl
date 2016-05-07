<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:template name="head">
    <head>
      <xsl:call-template name="stylesheet"/>
      <xsl:call-template name="javascript"/>
    </head>
  </xsl:template>


  <xsl:template name="stylesheet">
    <link>
      <xsl:attribute name="href"><xsl:value-of select="/data/system/baseUrl"/>/css/themes/bootstrap.css</xsl:attribute>
    </link>
    <link href="{{ app.request.baseUrl }}/css/themes/{{app.theme}}.css" rel="stylesheet">
      <xsl:attribute name="href"><xsl:value-of select="/data/system/baseUrl"/>/css/themes/<xsl:value-of select="/data/system/globalconfig/theme"/>.css</xsl:attribute>
    </link>
    <link rel="stylesheet/less" type="text/css">
      <xsl:attribute name="href"><xsl:value-of select="/data/system/baseUrl"/>/css/style.less</xsl:attribute>
    </link>
  </xsl:template>

  <xsl:template name="javascript">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/2.5.0/less.min.js"></script>
    <script type="text/javascript">
      var baseUrl = "<xsl:value-of select="/data/system/baseUrl"/>";
    </script>
    <script src="{/data/system/baseUrl}/js/vendor/jquery-2.1.3.js"></script>
    <script src="{/data/system/baseUrl}/js/vendor/advanced.js"></script>
    <script src="{/data/system/baseUrl}/js/vendor/wysihtml5-0.3.0.js"></script>
    <script src="{/data/system/baseUrl}/js/custom.js"></script>
  </xsl:template>

</xsl:stylesheet>