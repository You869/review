function searchRecipes() {
    const searchQuery = document.getElementById('search').value; // 検索クエリを取得
    const resultsContainer = document.getElementById('results'); // 結果を表示するコンテナ

    // 検索クエリが空の場合、結果をクリア
    if (searchQuery.length < 1) {
        resultsContainer.innerHTML = '';
        return;
    }

    // AJAXリクエストの作成
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'search.php?title=' + encodeURIComponent(searchQuery), true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // レスポンスをJSON形式で処理
            const data = JSON.parse(xhr.responseText);
            displayResults(data);
        }
    };
    xhr.send();
}

function displayResults(data) {
    const resultsContainer = document.getElementById('results');
    
    // 結果がなかった場合の処理
    if (data.length === 0) {
        resultsContainer.innerHTML = '<p>検索結果がありません。</p>';
        return;
    }

// 結果をHTMLに表示
let html = '<ul>';
data.forEach(item => {
    html += `<li>
               <p>${item.username} さん</p>
               <p>料理名:${item.dishname}／評価:${item.evaluation}／店名:${item.restaurantname}</p>
               <div class="img-container">
                ${item.image_url ? `<a href="${item.image_url}" target="_blank"><img src="${item.image_url}" alt="料理画像" class="result-image">` : '画像なし'}</a>
               </div>
               <p class="review">レビュー:${item.review}</p>
              </li>`;
});
html += '</ul>';

resultsContainer.innerHTML = html;

}
