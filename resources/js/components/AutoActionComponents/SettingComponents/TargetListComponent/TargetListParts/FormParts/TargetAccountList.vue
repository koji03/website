<template>
    <ul v-if="!targetListFormLoad" class="c-setting__list">
        <li class="c-setting__list__item" 
        v-for="item in targetList" :key="item.index">@{{ item }}
        <i class="fas fa-minus-circle c-setting__list__icon"  
        @click="deleteItem(item)"></i></li> 
    </ul>
</template>

<script>
import { mapState } from 'vuex'
import { OK } from '../../../../../../util'

export default {
    data(){
        return{
            message:{
                failed_register:'削除に失敗しました。ブラウザを更新後もう一度お試しください。',
                confirm:'をターゲットリストから削除しますか？'
            }
        }
    },
    methods:{
        //ターゲットリストのアカウントを削除する。 DBとautoActionSettingから消す。
        async deleteItem(id){
            if(!confirm(id+this.message.confirm)){
                return false
            }
            this.$store.commit('autoActionSetting/setTargetListError','')
            this.$store.commit('autoActionSetting/setTargetListFormLoad',true)
            const response = await axios.post('/api/deleteUserFromTargetList',{
                target_id:id
            })
            this.$store.commit('autoActionSetting/setTargetListFormLoad',false)
            if(response.status !== OK){
            this.$store.commit('autoActionSetting/setTargetListError',this.message.failed_register)
                return false
            }
            let array = this.targetList
            const index = array.indexOf(id)
            array.splice(index, 1)
            this.$store.commit('autoActionSetting/setTargetList',array)
        }
    },
    computed:{
        ...mapState({
            targetList: state => state.autoActionSetting.targetList,
            targetListFormLoad: state => state.autoActionSetting.targetListFormLoad,
        }),
    },
}
</script>