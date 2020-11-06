<template>
    <div class="u-list__background__color">
        <div class="c-setting__wrap">
            <TargetListButton/>
            <TargetListForm/>
        </div>
    </div>
</template>
<script>
import { OK } from '../../../../util'
import TargetListForm from './TargetListParts/TargetListForm'
import TargetListButton from './TargetListParts/TargetListButton'
import { mapState } from 'vuex'

export default {
    components:{
        TargetListForm,
        TargetListButton
    },
    methods:{
        //DBからターゲットリストを取得し、autoActionSettingに格納する。
        async getTargetList(){
            let targetList = []
            this.$store.commit('autoActionSetting/setTargetListFormLoad',true)
            const response = await axios.post('/api/getTargetList',{
                twitter_screen_name:this.twitter_screen_name
            });
            this.$store.commit('autoActionSetting/setTargetListFormLoad',false)
            if(response.status !== OK){
                this.$store.commit('error/setCode',response.status)
                return false
            }
            for(let item in response.data){
                targetList.push(response.data[item].target)
            }
            this.$store.commit('autoActionSetting/setTargetList',targetList)
        },
        clearError(){
            this.$store.commit('autoActionSetting/setTargetListError','')
        }
    },
    created(){
        this.getTargetList()
        this.clearError()

    },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
        }),
    },
    watch:{
        '$route':function(){
            this.getTargetList()
            this.clearError()
        }
    }
}
</script>