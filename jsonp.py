import json
import cymysql
from watson_developer_cloud import ToneAnalyzerV3

tone_analyzer = ToneAnalyzerV3(username="yourBluemixAppUsername",password="yourBluemixAppPass",version='2016-05-19 ')

try:
	cnx = cymysql.connect(host='127.0.0.1', user='admin', passwd='admin', db='analyserDB')
except:
	print ("oops")

c = cnx.cursor()

try:
	c.execute("SELECT * FROM comments WHERE INSTR(LOWER(body), ' trump ') > 0 ORDER BY time LIMIT 900")
except:
	print ("Problem?")

res=c.fetchall();
i=0;

for (commID, body, time) in res:
	i+=1
	print (i)
	j = tone_analyzer.tone(text=body)
	v = (commID, time.strftime('%Y-%m-%d %H:%M:%S'), j['document_tone']['tone_categories'][0]['tones'][0]['score'], j['document_tone']['tone_categories'][0]['tones'][1]['score'], j['document_tone']['tone_categories'][0]['tones'][2]['score'], j['document_tone']['tone_categories'][0]['tones'][3]['score'], j['document_tone']['tone_categories'][0]['tones'][4]['score'], j['document_tone']['tone_categories'][1]['tones'][0]['score'], j['document_tone']['tone_categories'][1]['tones'][1]['score'], j['document_tone']['tone_categories'][1]['tones'][2]['score'], j['document_tone']['tone_categories'][2]['tones'][0]['score'], j['document_tone']['tone_categories'][2]['tones'][1]['score'], j['document_tone']['tone_categories'][2]['tones'][2]['score'], j['document_tone']['tone_categories'][2]['tones'][3]['score'], j['document_tone']['tone_categories'][2]['tones'][4]['score'])
	try:
		c.execute("insert into tones (commID,time,anger,disgust,fear,joy,sadness,analytical,confident,tentative,openness,conscientiousness,extraversion,agreeableness,emotional) values (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)", v)
	except:
		print ("Problem?")
	cnx.commit()

#j = json.loads('{"document_tone": {"tone_categories": [{"tones": [{"score": 0.25482,"tone_id": "anger","tone_name": "Anger"},{"score": 0.345816,"tone_id": "disgust","tone_name": "Disgust"}]}]}}')
#print (j['document_tone']['tone_categories'][0]['tones'][0]['score'])
