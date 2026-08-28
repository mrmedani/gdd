#!/bin/bash
# Simulation : cookie de session EXPIRE (comme apres une longue inactivite du navigateur)
cd "C:/Users/Luckatchi/GDD"
# 1. je recupere une vraie session puis je la fais expirer en supprimant le fichier
SESS=$(ls -t storage/framework/sessions/ | head -1)
echo "session test: $SESS"
# je copie le cookie de la session valide
cp /tmp/gdd_c9.txt /tmp/gdd_expired.txt
# je supprime le FICHIER de session que ce cookie reference -> cookie valide, session inexistante
curl -s -b /tmp/gdd_c9.txt http://127.0.0.1:8080/ai-chat -o /dev/null -c /tmp/track.txt -w "avant: %{http_code}\n"
NEWSESS=$(grep laravel_session /tmp/track.txt | awk '{print $7}')
echo "cookie session: ${NEWSESS:0:12}..."
rm -f "storage/framework/sessions/$NEWSESS"
# 2. je rejoue la requete avec ce cookie dont la session n existe plus
curl -s -b /tmp/track.txt http://127.0.0.1:8080/ai-chat -o /dev/null -w "apres destruction session: %{http_code}\n"
