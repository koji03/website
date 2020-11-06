<template>
    <div>
        <div class="p-banner">
            <RouterLink class="p-banner__message" to="/form/register">
                {{ siteTitle }}で今すぐ<span class="p-banner__message--orange">自動フォロー</span>を体験する
                <ul class="p-banner__list u-margin__banner--top">
                    <li class="p-banner__list__item">
                        フォローしたいユーザーの条件を設定！！！<br>
                        条件に合ったユーザーを自動で探してフォロー！！！
                        </li>
                    <li class="p-banner__list__item">・<span class="p-banner__list--detail">自動ツイート</span> /日時を指定してツイートを登録すると指定日時にツイート</li>
                    <li class="p-banner__list__item">・<span class="p-banner__list--detail">自動いいね</span> /登録したキーワードが含まれているツイートをいいね</li>
                    <li class="p-banner__list__item">・<span class="p-banner__list--detail">自動アンフォロー</span> /ツイートをしない、フォロー返しをしないユーザーのフォローを解除</li>
                </ul>
            </RouterLink>
            <img :src="'/images/header.jpg'" alt="" class="p-banner__image">
        </div>
        <div class="u-margin--top__large u-margin--bottom__large p-top">
            <h1 class="u-margin__title--bottom p-top__title">アカウントを作成しよう！</h1>
            <div class="p-top__box">
                <div class="p-top__box__image">
                    <img :src="'/images/sns.jpg'" alt="" class="p-image__large">
                </div>
                <div class="p-top__box__form">
                    <register-form/>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import RegisterForm from '../components/FormComponents/RegisterForm.vue'
export default {
  components: {
    RegisterForm
  },
  methods:{
    clearError () { 
        this.$store.commit('auth/setRegisterErrorMessages', null)
    }
  },
  created () { 
    this.clearError() //ページを移動して戻ったときにエラーが表示されないようにする
  },
  computed: {
        ...mapState({
            siteTitle: state => state.twitter.siteTitle,
        }),
    },   
  
}
</script>