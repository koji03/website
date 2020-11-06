<template>
    <div class="c-setting__wrap p-following__image">
        <div class="p-following">
            <div class="spinner">
                <div class="cube1"></div>
                <div class="cube2"></div>
            </div>
            <div class="p-following__message">
                <div>
                    自動機能を開始しました。すべての自動機能が完了したら登録しているメールアドレス宛に通知をします。<br>
                    ページを離れても大丈夫です。
                </div>
            </div>
            <div class="u-margin__summary--top">
                <Summary/>
            </div>
        </div>
    </div>
</template>

<script>
//ターゲットリストの人数
import { mapState } from 'vuex'
import Summary from '../Summary'
export default {
    components:{
        Summary
    },
}
</script>