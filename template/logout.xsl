<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:import href="base.xsl"/>
  <xsl:import href="form.xsl"/>


  <xsl:template match="/">
    <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
    <html>
      <xsl:call-template name="head"/>
      <body class="bg-darkTeal">
        <div class="login-form padding20 block-shadow">
          <h1 class="text-light">Logout from testonaut</h1>
          <hr class="thin"/>
          <br />
          <br />
          <xsl:call-template name="message"/>
          <br />
          <br />
          <a class="button primary" href="{/data/system/baseUrl}">Back</a>
        </div>
        <script type="text/javascript">

          $(function(){
          var form = $(".login-form");
          form.css({
          opacity: 1,
          "-webkit-transform": "scale(1)",
          "transform": "scale(1)",
          "-webkit-transition": ".5s",
          "transition": ".5s"
          });
          });
        </script>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>