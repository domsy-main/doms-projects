from django.http import JsonResponse
from django.views.decorators.csrf import csrf_exempt
import json
from textblob import TextBlob
from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer

@csrf_exempt
def analyze_sentiment(request):
    if request.method == 'POST':
        data = json.loads(request.body)
        text = data.get('text', '')

        # TextBlob analysis
        analysis = TextBlob(text)
        polarity = analysis.sentiment.polarity

        if polarity > 0:
            textblob_sentiment = "Positive"
        elif polarity < 0:
            textblob_sentiment = "Negative"
        else:
            textblob_sentiment = "Neutral"

        # VADER analysis
        analyzer = SentimentIntensityAnalyzer()
        vader_scores = analyzer.polarity_scores(text)
        vader_sentiment = (
            "Positive" if vader_scores['compound'] > 0.05 else
            "Negative" if vader_scores['compound'] < -0.05 else
            "Neutral"
        )

        return JsonResponse({
            "textblob_sentiment": textblob_sentiment,
            "vader_sentiment": vader_sentiment,
            "vader_scores": vader_scores
        })
