<xsl:stylesheet
        version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:template name="login-form">

    <div class="login-form padding20 block-shadow">
      <form action="" method="POST">
        <h1 class="text-light">Login to testonaut</h1>
        <hr class="thin"/>
        <br />
        <div class="input-control text full-size" data-role="input">
          <label for="user_login">User email:</label>
          <input type="text" name="username" id="user_login"/>
          <button class="button helper-button clear"><span class="mif-cross"></span></button>
        </div>
        <br />
        <br />
        <div class="input-control password full-size" data-role="input">
          <label for="user_password">User password:</label>
          <input type="password" name="password" id="user_password"/>
          <button class="button helper-button reveal"><span class="mif-looks"></span></button>
        </div>
        <br />
        <br />
        <div class="form-actions">
          <button type="submit" class="button primary">Login to</button>
          <button type="button" class="button link">Cancel</button>
        </div>
      </form>
    </div>

    <xsl:call-template name="message"/>

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

    <xsl:if test="/data/message/text()">
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