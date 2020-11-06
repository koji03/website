<template>
    <div>
        <i class="far fa-heart c-setting__icon" @click="isChangeForm"></i>
        <h2 class="c-setting__title u-margin__h2--bottom" @click="isChangeForm">いいね</h2>
        <p>指定したキーワードが含まれているツイートをいいねします。</p>
        <div class="c-setting__switch" @click="isChangeForm">
        <i class="fas fa-toggle-on c-icon--blue"  v-if="isLikeOpen"></i>
        <i class="fas fa-toggle-off c-icon--blue"  v-else></i>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'

export default {
    methods:{
        //formを表示する。 true　falseが入れ替わる。
        isChangeForm(){
            this.$store.commit('autoActionSetting/setLikeOpen')
        }
    },
    computed:{
        ...mapState({
            isLikeOpen: state => state.autoActionSetting.isLikeOpen
        }),
    },
}
</script>