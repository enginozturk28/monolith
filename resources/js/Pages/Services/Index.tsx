import MainLayout from '@/Layouts/MainLayout';
import Section from '@/Components/Layout/Section';
import PageHeader from '@/Components/Layout/PageHeader';
import ServiceCard from '@/Components/Content/ServiceCard';
import FadeIn from '@/Components/Motion/FadeIn';
import SeoHead from '@/Components/Seo/SeoHead';

type Service = {
    slug: string;
    title: string;
    icon?: string | null;
    summary?: string | null;
};

type Props = {
    services: Service[];
};

export default function ServicesIndex({ services }: Props) {
    return (
        <MainLayout>
            <SeoHead
                title="Faaliyet Alanları"
                description="Ceza, Aile, Miras, Marka & Patent, Gayrimenkul, Vergi, İş, Ticaret ve daha fazlası — hukukun farklı alanlarında bireysel ve kurumsal müvekkillere profesyonel destek."
            />

            <PageHeader
                title="Faaliyet Alanları"
                description="Hukukun farklı alanlarında bireysel ve kurumsal müvekkillere profesyonel destek sunuyoruz. Her alan için dosyanıza özgü bir değerlendirme yapılır."
            />

            <Section size="wide">
                <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    {services.map((service, i) => (
                        <FadeIn key={service.slug} delay={0.03 * i}>
                            <ServiceCard {...service} />
                        </FadeIn>
                    ))}
                </div>
            </Section>
        </MainLayout>
    );
}
