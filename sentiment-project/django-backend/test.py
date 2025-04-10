from vaderSentiment.vaderSentiment import SentimentIntensityAnalyzer

text = "I don't like you"
analyzer = SentimentIntensityAnalyzer()
print(analyzer.polarity_scores(text))
