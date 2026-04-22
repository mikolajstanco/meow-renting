import mysql.connector
from datetime import datetime
import requests
from time import sleep
from discord_webhook import DiscordWebhook, DiscordEmbed
import random
import string
import pytz
import os
from dotenv import load_dotenv

load_dotenv('../.env')

global session
session = None

def generate_random_string(length):
    random_string = ''.join(random.choices(string.ascii_letters + string.digits, k=length))
    return random_string

def toFileRead():
    try:
        with open("password.txt", "r") as plik:
            return plik.read()
    except FileNotFoundError:
        print("Błąd: Plik password.txt nie istnieje!")
        return None
    except IOError as e:
        print(f"Błąd odczytu pliku: {e}")
        return None

def toFileWrite():
    dane = generate_random_string(10)
    try:
        with open("password.txt", "w") as plik:
            plik.write(dane)
        return dane
    except IOError as e:
        print(f"Błąd zapisu do pliku password.txt: {e}")
        return None

def login(oldPassword): 
    headers = {
        'authority': os.getenv("VENDOR_LINK_ORIGIN"),
        'accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
        'content-type': 'application/x-www-form-urlencoded',
        'origin': os.getenv("VENDOR_LINK_ORIGIN"),
        'referer': f'{os.getenv("VENDOR_LINK_ORIGIN")}/login',
        'user-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36',
    }

    data = {
        'email': os.getenv("VENDOR_EMAIL"),
        'password': f'{oldPassword}',
    }


    # response = session.post(f'{os.getenv("VENDOR_LINK_ORIGIN")}/login', headers=headers, data=data, timeout=10)
    # response.raise_for_status()
    print("EMULACJA: Pominięto próbę logowania do Vendora.")

def unBindUsers():
    global session
    headers = {
        'authority': os.getenv("VENDOR_LINK_ORIGIN"),
        'content-type': 'application/x-www-form-urlencoded',
        'origin': os.getenv("VENDOR_LINK_ORIGIN"),
        'referer': f'{os.getenv("VENDOR_LINK_ORIGIN")}/manage',
        'user-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36',
    }

    data = {
        'userkey': os.getenv("VENDOR_USERKEY"),
    }

    # response = session.post(os.getenv("VENDOR_LINK_UNBIND_USERS"), headers=headers, data=data, timeout=10)
    # response.raise_for_status()
    print("EMULACJA: Pominięto odpinanie użytkowników na stronie Vendora.")

def passwordchange():
    global session
    newPassword = toFileWrite()
    
    if not newPassword:
        raise Exception("Nie udało się wygenerować nowego hasła.")

    headers = {
        'authority': os.getenv("VENDOR_LINK_ORIGIN"),
        'content-type': 'application/x-www-form-urlencoded',
        'origin': os.getenv("VENDOR_LINK_ORIGIN"),
        'referer': f'{os.getenv("VENDOR_LINK_ORIGIN")}/manage',
        'user-agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36',
    }

    data = {
        'email': os.getenv("VENDOR_EMAIL"),
        'password': f'{newPassword}',
    }

    # response = session.post(os.getenv("VENDOR_LINK_CHANGE_PASSWORD"), headers=headers, data=data, timeout=10)
    # response.raise_for_status()
    print("EMULACJA: Hasło wygenerowane pomyślnie. Pominięto wysyłanie go do Vendora.")

def VENDORpasswordchange():
    oldPassword = toFileRead()
    if not oldPassword:
        return False

    try:
        login(oldPassword)
        unBindUsers()
        passwordchange()
        return True
    except requests.exceptions.RequestException as e:
        print(f"Błąd połączenia z API Vendor: {e}")
        return False
    except Exception as e:
        print(f"Niespodziewany błąd przy zmianie hasła: {e}")
        return False

def sendWebhook():
    try:
        newPassword = toFileRead()
        webUrl = os.getenv("DISCORD_SERVER_WEBHOOK")
        webhook = DiscordWebhook(url=f'{webUrl}', timeout=10)
        embed = DiscordEmbed(title='VENDOR LOGIN DATA', color='FF0000')
        embed.add_embed_field(name='', value=f'Login: {os.getenv("VENDOR_EMAIL")}', inline=False)
        embed.add_embed_field(name='', value=f'Password: {newPassword}')    
        embed.set_timestamp()
        webhook.add_embed(embed)
        webhook.execute()
    except Exception as e:
        print(f"Błąd wysyłania Webhooka: {e}")

def dbdelete():
    sql_conn = None
    mycursor = None
    try:
        sql_conn = mysql.connector.connect(
            host=os.getenv("DB_HOST"),
            database=os.getenv("DB_NAME"),
            user=os.getenv("DB_USER"),
            password=os.getenv("DB_PASSWORD")
        )
        mycursor = sql_conn.cursor()

        tz = pytz.timezone('Europe/Warsaw')
        current_dateTimeFormatted = datetime.now(tz).strftime('%Y-%m-%d %H:%M:%S')

        mycursor.execute(f'SELECT discordID from users WHERE rentTime < "{current_dateTimeFormatted}" AND rentTime != "2000-01-01 00:00:00"')
        myresult = mycursor.fetchall()

        bot_token = os.getenv("DISCORD_BOT_TOKEN")

        if myresult:
            success = VENDORpasswordchange()
            
            if success:
                
                header = {"Authorization": f'Bot {bot_token}', "Content-Length": "0"}
                
                for discordID in myresult: 
                    url = f'https://discord.com/api/v10/guilds/1107813083554005072/members/{discordID[0]}/roles/1108095202918408232'
                    
                    try:
                        requests.delete(url=url, headers=header, timeout=10)
                        print(f"Zabrano rangę dla ID: {discordID[0]}")
                    except requests.exceptions.RequestException as e:
                        print(f"Błąd usuwania roli na Discordzie dla ID {discordID[0]}: {e}")
                    
                    sql = "UPDATE users SET rentTime = '2000-01-01 00:00:00' WHERE discordID = %s"
                    val = (discordID[0], )
                    mycursor.execute(sql, val)
                     
                sql_conn.commit()
                sendWebhook()
            else:
                print("Pominięto aktualizację bazy i ról na Discordzie, ponieważ zmiana hasła się nie powiodła.")

    except mysql.connector.Error as err:
        print(f"Błąd bazy danych w dbdelete: {err}")
    except Exception as e:
        print(f"Krytyczny błąd w dbdelete: {e}")
    finally:
        if mycursor:
            mycursor.close()
        if sql_conn and sql_conn.is_connected():
            sql_conn.close()

def main():
    global session
    while True:
        try:
            session = requests.Session() 
            dbdelete()
        except Exception as e:
            print(f"Awaria pętli głównej: {e}")
            
        print("Cykl zakończony. Oczekiwanie...")
        sleep(60)

if __name__ == "__main__":
    main()