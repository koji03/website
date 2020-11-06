<template>
    <div class="c-summary">
        <div class="c-summary--title u-margin__summary">注意事項</div>
        <div class="c-summary__wrap">
            <h2 class="c-summary__wrap--title">自動機能が完了した</h2>
            <p class="c-summary__wrap--text ">
                アカウント登録時に設定されたメールに完了したことを通知します。
            </p>
        </div>
        <div class="c-summary__wrap u-margin__summary__wrap">
            <h2 class="c-summary__wrap--title">自動機能開始後にブラウザを閉じたりPCを閉じた</h2>
            <p class="c-summary__wrap--text">
                自動機能は実行され続けるので閉じても大丈夫です。
            </p>
        </div>
        <div class="c-summary__wrap u-margin__summary__wrap">
            <h2 class="c-summary__wrap--title ">ツイッターアカウントがロックまたは凍結された</h2>
            <p class="c-summary__wrap--text">
                自動機能は停止され、アカウント登録時に設定したメールアドレスに通知をします。
                <br>
                ロックや凍結を解除後に手動で自動機能を再開するようお願いします。
                <br>
                再開後は続きからフォロー、アンフォローが始まります。
            </p>
        </div>
        <div class="c-summary__wrap u-margin__summary__wrap">
            <h2 class="c-summary__wrap--title">API制限にかかった</h2>
            <p class="c-summary__wrap--text">
                アカウント登録時に設定したメールアドレスにAPIが制限されたことを通知をします。
                <br>
                フォロー、アンフォロー、いいねはAPI制限解除後に自動で再開します。
            </p>
        </div>
        <div class="c-summary__wrap u-margin__summary__wrap">
            <h2 class="c-summary__wrap--title">ターゲットリストの仕様</h2>
            <p class="c-summary__wrap--text ">
                ターゲットリストに登録した順番にフォロワーターゲットリストを作成します。
                <br>
                一つのアカウントのフォロワーターゲットリストを作成したらフォローを開始します。
                <br>
                全てのフォローを終えたらターゲットリストからそのユーザーは削除され、次のユーザーのフォロワーターゲットリストを作成します。
                <br>
                
            </p>
        </div>
       <div class="c-summary__wrap u-margin__summary__wrap">
            <h2 class="c-summary__wrap--title">フォローの仕様</h2>
            <p class="c-summary__wrap--text ">
                日本人のみをフォローします。(プロフィールに日本語の50音が含まれているかで判断します)
            </p>
        </div>
        <div class="c-summary__wrap u-margin__summary__wrap">
            <h2 class="c-summary__wrap--title">アンフォローの仕様</h2>
            <p class="c-summary__wrap--text ">
                アンフォローの設定をONにしている場合フォローしているユーザー数が5000を超えたら自動的に始まります。
                <br>
                一度アンフォローしたユーザーはフォローしません。
            </p>
        </div>
        <div class="c-summary__wrap u-margin__summary__wrap">
            <h2 class="c-summary__wrap--title">いいねの仕様</h2>
            <p class="c-summary__wrap--text ">
                タイムラインから最大で3000件までツイートを取得し、該当のツイートにいいねをします。
                <br>
                取得したタイムラインから新しい順に10ツイートまでいいねをします。
            </p>
        </div>
    </div>
</template>