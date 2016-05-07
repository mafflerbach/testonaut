<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:template name="login-form">
    <form class="uk-form" action="" method="POST">
      <fieldset data-uk-margin="data-uk-margin">
        <legend>login</legend>
        <div class="uk-form-row">
          <input type="text" name="username" placeholder=""/>
        </div>
        <div class="uk-form-row">
          <input type="password" name="password" placeholder=""/>
        </div>

        <div class="uk-form-row">
          <input type="hidden" name="action" value="login"/>

          <input type="submit" class="uk-button uk-button-primary" name="login" value="Login"/>
          <a class="uk-button" href="{/data/system/baseUrl}reset/">forget password ?</a>
        </div>

      </fieldset>
    </form>
    <br/>

    <xsl:call-template name="message"/>

  </xsl:template>

  <xsl:template name="reset-form">
    <form class="uk-form" action="" method="POST">
      <fieldset data-uk-margin="data-uk-margin">
        <legend>Reset</legend>
        <div class="uk-form-row">
          <input type="text" name="email" placeholder=""/>
        </div>

        <div class="uk-form-row">
          <input type="hidden" name="action" value="reset"/>
          <input type="submit" class="uk-button uk-button-primary" name="reset" value="Reset"/>
        </div>

      </fieldset>
    </form>
    <br/>

    <xsl:call-template name="message"/>

  </xsl:template>

  <xsl:template name="message">

    <xsl:if test="/data/message">
      <div class="uk-width-medium-1-2 uk-container-center">
        <div class="uk-panel uk-panel-box uk-panel-box-primary">
          <xsl:value-of select="/data/message"/>
        </div>
      </div>
    </xsl:if>

  </xsl:template>


  <xsl:template match="input" mode="text">
    <input type="text" placeholder=""/>
  </xsl:template>

  <xsl:template match="input" mode="password">
    <input type="password" placeholder=""/>
  </xsl:template>

</xsl:stylesheet>