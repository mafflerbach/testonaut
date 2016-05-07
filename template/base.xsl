<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
  <xsl:output method="html" />
  <xsl:template name="head">
    <head>

      <xsl:call-template name="stylesheet"/>
      <xsl:call-template name="javascript"/>

    </head>
  </xsl:template>


  <xsl:template name="stylesheet">
    <link rel="stylesheet" href="{/data/system/baseUrl}css/gradient.css" />
  </xsl:template>

  <xsl:template name="javascript">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/uikit/2.26.2/js/uikit.min.js" ></script>
  </xsl:template>

</xsl:stylesheet>