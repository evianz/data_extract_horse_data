#!/public/bin/bash

horse_name=(Choisir Reset "Bel\ Esprit" "Redoute\'s\ Choice")
IFS=""
rank=12

for i in ${horse_name[@]}; do
	CMD="php index.php $i $rank > ./result/$rank\.$i.csv"
	rank=$(($rank+1))

	echo "$CMD" 
	eval "$CMD"
done
exit 0
