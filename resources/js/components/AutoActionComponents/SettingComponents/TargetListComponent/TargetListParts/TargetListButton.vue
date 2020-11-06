<template>
    <div>
        <i class="fas fa-clipboard-list c-setting__icon" @click="openForm"></i>
        <h2 class="c-setting__title u-margin__h2--bottom" @click="openForm">ターゲットリスト</h2>
        <p>
            ターゲットリストに登録したアカウントのフォロワーをフォローします。
        </p>
        <div class="c-setting__switch u-margin__switch">
            <i class="fas fa-toggle-on c-icon--blue"  v-if="isTargetListOpen" @click="openForm"></i>
            <i class="fas fa-toggle-off c-icon--blue"  v-else @click="openForm"></i>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
export default {
    methods:{
        //formを表示する。 true false　が入れ替わる.
        openForm(){
            this.$store.commit('autoActionSetting/setIsTargetListOpen')
        },
    },
    computed:{
        ...mapState({
            isTargetListOpen: state => state.autoActionSetting.isTargetListOpen,
        }),
    }
}
</script>