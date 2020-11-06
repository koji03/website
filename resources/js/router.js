import Vue from 'vue'
import VueRouter from 'vue-router'

// ページコンポーネントをインポートする
import Top from './pages/Top.vue'
import FormGroup from './pages/FormGroup.vue'
import PassReset from './pages/PassReset.vue'
import Kamitter from './pages/AutoActionSetting.vue'
import Home from './pages/home.vue'
import AccountData from './pages/AccountData.vue'

import store from './store'

import SystemError from './pages/errors/System'
import NotFound from './pages/errors/NotFound.vue'
// VueRouterプラグインを使用する
// これによって<RouterView />コンポーネントなどを使うことができる
Vue.use(VueRouter)

// パスとコンポーネントのマッピング
const routes = [
  {
    path: '/',
    component: Top,
    meta: { title: 'ツイッター自動フォロー', desc: '誰でも簡単にツイッターの自動フォロー、自動いいね、自動アンフォローができます。フォロー数を増やしたい場合や好きな話題にいいねをしたいときに便利です。' },
    beforeEnter(to,from,next){
      if(store.getters['auth/check']){
        //ログインしている場合はトップページに遷移
        next('/kamitter/home')
      }else{
        //していない場合はそのままログインページを表示
        next()
      }
    }
  },
  {
    path: '/form/:form',
    component: FormGroup,
    meta: { title: 'ツイッター自動フォロー/フォーム', desc: 'フォーム画面です。' },
    props:true,//propsを渡す :form
    beforeEnter(to,from,next){
      if(store.getters['auth/check']){
        //ログインしている場合はトップページに遷移
        next('/kamitter/home')
      }else{
        //していない場合はそのままログインページを表示
        window.scroll(0, 0);
        next()
      }
    }
  },
  {
    path: '/password/:reset',
    meta: { title: 'ツイッター自動フォロー/パスワードリセット', desc: 'パスワードをリセットします。' },
    component: PassReset,
    beforeEnter(to,from,next){
      if(store.getters['auth/check']){
        //ログインしている場合はトップページに遷移
        next('/kamitter/home')
      }else{
        //していない場合はそのままログインページを表示
        next()
      }
    }
  },
  {
    path: '/kamitter/home',
    meta: { title: 'ツイッター自動フォロー', desc: 'お手軽に自動フォロー、自動いいね、自動アンフォローをします。フォロー数を増やしたい人や好きな話題にいいねをしたいときに便利です。' },
    component:Home,
    beforeEnter(to,from,next){
      if(store.getters['auth/check']){
        next()
      }else{
        next('/form/login')
      }
    }
  },
  {
    path: '/kamitter/data/:screen_name',
    meta: { title: 'ツイッター自動フォロー', desc: 'アカウントのデータを閲覧します。' },
    component:AccountData,
    beforeEnter(to,from,next){
      if(store.getters['auth/check']){
        next()
      }
      else{
        next('/form/login')
      }
    }
  },
  {
    path: '/kamitter/:screen_name',
    meta: { title: 'ツイッター自動フォロー/アカウントページ', desc: '自動機能の設定をします。' },
    component:Kamitter,
    beforeEnter(to,from,next){
      if(store.getters['auth/check']){
        next()
      }else{
        next('/form/login')
      }
    }
  },
  {
    path: '/500',
    component:SystemError
  },
  {
    path: '*',
    component: NotFound
  }
]

// VueRouterインスタンスを作成する
const router = new VueRouter({
    mode: 'history', 
    routes
})

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router