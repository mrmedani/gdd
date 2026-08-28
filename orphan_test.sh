#!/bin/bash
# Test fiable : cookie valide mais session DETRUIE cote serveur
cd "C:/Users/Luckatchi/GDD"
rm -f /tmp/gdd_x.txt
curl -s -c /tmp/gdd_x.txt -o /dev/null http://127.0.0.1:8080/login
TOKEN=$(curl -s -b /tmp/gdd_x.txt -c /tmp/gdd_x.txt http://127.0.0.1:8080/login | grep -oP 'name="_token" value="\K[^"]+' | head -1)
curl -s -b /tmp/gdd_x.txt -c /tmp/gdd_x.txt -o /dev/null -w "login: %{http_code}\n" -X POST http://127.0.0.1:8080/login -d "_token=${TOKEN}&email=admin@chronorex.ma&password=admin123"
# identifie le fichier de session utilise
NEWSESS=$(grep laravel_session /tmp/gdd_x.txt | awk '{print $7}')
echo "fichier session: ${NEWSESS:0:15}..."
ls storage/framework/sessions/ | grep -c "$NEWSESS" | xargs echo "fichier existe:"
# detruit le fichier -> simule expiration serveur (garbage collection) avec cookie restant
rm -f "storage/framework/sessions/$NEWSESS"
curl -s -b /tmp/gdd_x.txt http://127.0.0.1:8080/ai-chat -o /dev/null -w "requete avec cookie orphelin: %{http_code}\n"
