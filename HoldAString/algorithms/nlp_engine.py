import spacy
import sys
import json

nlp = spacy.load("en_core_web_sm")

def process_text(text):
    doc = nlp(text)
    results = {
        "person": "Someone",
        "date": None,
        "action": "interaction"
    }

    for ent in doc.ents:
        if ent.label_ == "PERSON":
            results["person"] = ent.text
        if ent.label_ == "DATE":
            results["date"] = ent.text

    for token in doc:
        if token.dep_ == "ROOT":
            results["action"] = token.lemma_

    return results

if __name__ == "__main__":
    if len(sys.argv) > 1:
        input_text = sys.argv[1]
        output = process_text(input_text)
        print(json.dumps(output))