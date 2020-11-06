<template>
    <div class="c-setting__wrap">
        <Button/>
        <Form/>
    </div>
</template>
<script>
import { mapState } from 'vuex'
import { OK } from '../../../../util'
import Button from './FollowerSearchParts/FollowerSearchButton'
import Form from './FollowerSearchParts/FollowerSearchForm'
export default {
    components:{
        Button,
        Form
    },
    methods:{
        //サーチワードリストをDBから取得する。
        //それをautoActionSettingに格納する。
        async getSearchWordList(){
            this.$store.commit('autoActionSetting/ResetWordList')
            this.$store.commit('autoActionSetting/setFollowerSearchLoad',true)
            const response = await axios.post('/api/getSearchWordList',{
                twitter_screen_name:this.twitter_screen_name
            });
            if(response.status !== OK){
                this.$store.commit('autoActionSetting/setFollowerSearchLoad',false)
                this.$store.commit('error/setCode',response.status)
                return false
            }
            await response.data.forEach(element => {
                this.$store.dispatch('autoActionSetting/setWordList',element)
                });
                this.$store.commit('autoActionSetting/setFollowerSearchLoad',false)
            },
            clearError(){
                this.$store.commit('autoActionSetting/setFollowerSearchError','')
            }
        },
    computed:{
        ...mapState({
            twitter_screen_name: state => state.twitter.twitter_screen_name,
        }),
    },
    created(){
        this.getSearchWordList()
        this.clearError()
    },
    watch:{
        '$route':function(){
            this.getSearchWordList()
            this.clearError()
        }
    }
}
</script>