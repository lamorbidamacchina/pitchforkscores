import sys
import urllib2
import time
import datetime
import MySQLdb
import xml.etree.ElementTree as ET
from bs4 import BeautifulSoup
from datetime import datetime

def main():
    # connect to database
    conn = MySQLdb.connect(host= "localhost",  user="[USERNAME]", passwd="[PASSWORD]", db="[DBNAME]")
    x = conn.cursor()
    conn.set_character_set('utf8')
    x.execute('SET NAMES utf8;')
    x.execute('SET CHARACTER SET utf8;')
    x.execute('SET character_set_connection=utf8;')

    # read albums' rss
    url = "http://pitchfork.com/rss/reviews/albums/"
    response = urllib2.urlopen(url)
    xmlfeed = response.read()
    f = open( 'albums.xml', 'w')
    f.write(xmlfeed)
    f.close()
    root = ET.parse('albums.xml').getroot()
    for child in root.iter('item'):
        title = child.find('title').text.encode('utf-8')
        description = child.find('description').text.encode('utf-8')
        link = child.find('link').text
        guid = child.find('guid').text
        pubDate = child.find('pubDate').text
        str_to_parse = pubDate.replace(" +0000","")
        review_date = datetime.strptime(str_to_parse, '%a, %d %b %Y %H:%M:%S')
        #print datetime_object
        # check if record is already stored
        try:
            x.execute("""SELECT id,title FROM pitchfork_albums WHERE title LIKE %s or guid LIKE %s""",(title,guid))
            results = x.fetchall()
            counter = 0
            for row in results:
                counter = counter + 1
                #id = row[0]
                #title = row[1]
            # add record
            if counter == 0:
                # print("album ",title," not stored yet")
                try:
                    x.execute("""INSERT into pitchfork_albums (title,description,link,guid,pubdate,review_date) values(%s,%s,%s,%s,%s,%s)""",(title,description,link,guid,pubDate,review_date))
                    conn.commit()
                    # go to album review page
                    html_review = urllib2.urlopen(link)
                    html_review_string = html_review.read()
                    # parse html page
                    html_soup = BeautifulSoup(html_review_string, 'html.parser')
                    # find what you need in html page...
                    soup_title = html_soup.title
                    h2 = html_soup.find("h2").get_text().encode('utf-8')
                    h1 = html_soup.find("h1").get_text().encode('utf-8')

                    score = html_soup.select("span.score")
                    if score:
                        score = score[0].get_text()
                    else:
                        score = ''

                    picture = html_soup.select("div.single-album-tombstone__art img")
                    if picture:
                        picture = picture[0]['src']
                    else:
                        picture = ''

                    creator = html_soup.select("a.authors-detail__display-name")
                    if creator:
                        creator = creator[0].get_text().encode('utf-8')
                    else:
                        creator = ''

                    genre = html_soup.select("a.genre-list__link")
                    if genre:
                        genre = genre[0].get_text().encode('utf-8')
                    else:
                        genre = ''

                    album_label = html_soup.select("li.labels-list__item")
                    if album_label:
                        album_label = album_label[0].get_text().encode('utf-8')
                    else:
                        album_label = ''

                    album_year = html_soup.select("span.single-album-tombstone__meta-year")
                    if album_year:
                        album_year = album_year[0].get_text().encode('utf-8')
                    else:
                        album_year = ''

                    print h2
                    print h1
                    print score
                    # update database with data from html page
                    try:
                        x.execute("""UPDATE pitchfork_albums SET album_author = %s, album_title=%s, score=%s, album_art=%s, creator=%s, genre=%s, album_label=%s, album_year=%s WHERE guid=%s""",(h2,h1,score,picture,creator,genre,album_label,album_year,guid))
                        conn.commit()
                    except (MySQLdb.Error, MySQLdb.Warning) as e:
                        print(e)
                        print("oh no! update problem!")
                        conn.rollback()

                    #f2 = open( 'review.html', 'w')
                    #f2.write(html_review_string)
                    #f2.close()
                    # grab score and picture
                    # update record on database
                # print error
                except (MySQLdb.Error, MySQLdb.Warning) as e:
                    print(e)
                    print("oh no! insert problem!")
                    conn.rollback()

          #conn.commit()
        except (MySQLdb.Error, MySQLdb.Warning) as e:
          print(e)
          print("oh no! select problem!")
          conn.rollback()
    conn.close()





    # close loop
    # close connection to database


if __name__ == '__main__':
  main()
