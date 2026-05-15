const newsContainer = document.getElementById("news-container");

function showNewsDetail(article) {
  const detailContainer = document.getElementById("news-detail-container");
  const detailSection = document.getElementById("news-detail");

  if (!article || !detailContainer || !detailSection) {
    return;
  }

  detailContainer.innerHTML = "";

  const fullArticle = document.createElement("article");
  fullArticle.classList.add("news-detail-article");

  const fullTitle = document.createElement("h3");
  fullTitle.textContent = article.dataset.title || "";

  const fullDate = document.createElement("p");
  fullDate.classList.add("news-detail-date");
  fullDate.textContent = article.dataset.date || "";

  const fullText = document.createElement("p");
  fullText.textContent = article.dataset.content || "";

  fullArticle.appendChild(fullTitle);
  fullArticle.appendChild(fullDate);
  fullArticle.appendChild(fullText);

  const budgetLink = document.createElement("a");
  budgetLink.href = "views/get_a_quote.php";
  budgetLink.textContent = "Request a quote";
  budgetLink.classList.add("news-budget-link");

  fullArticle.appendChild(budgetLink);
  detailContainer.appendChild(fullArticle);

  window.scrollTo({
    top: detailSection.offsetTop - 80,
    behavior: "smooth",
  });
}

window.showNewsDetail = showNewsDetail;

if (newsContainer) {
  newsContainer.addEventListener("click", (event) => {
    const article = event.target.closest(".news-card");

    if (!article) {
      return;
    }

    showNewsDetail(article);
  });
}
